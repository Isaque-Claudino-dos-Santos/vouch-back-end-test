<?php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/autoload.php';

use \Lib\{Database, MsgQueue, Env};
use \Models\Message;
use \Constants\MsgTypeEnum;

const CREATE_MESSAGES_TABLE_SCHEMA = <<<SQL
    CREATE TABLE IF NOT EXISTS messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        value VARCHAR(200) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
SQL;


$queueKeyFromArg = $argv[1] ?? null;


class Daemon
{
    private readonly Env $env;
    private readonly MsgQueue $queue;
    private readonly PDO $pdo;
    private readonly Message $messageModel;
    private bool $processStop = false;
    private int $queueKey;

    public function __construct()
    {
        $this->env = new Env('.env');
        $this->queueKey = $queueKeyFromArg ?? $this->env->get('QUEUE_KEY');
        $this->queue = new MsgQueue($this->queueKey);
        $this->pdo = new Database($this->env)->connect();
        $this->messageModel = new Message();
    }

    private function start(): void
    {
        echo "\n --- Daemon Started ---\n";

        if ($this->queue->exists()) {
            echo "\n - Started queue \"{$this->queueKey}\" successfully";
        }

        $this->pdo->exec(CREATE_MESSAGES_TABLE_SCHEMA);

        echo "\n - Create table \"messages\" if not exists table. \n\n";
    }


    private function process(): void
    {
        $messageToSave = $this->queue->receiveStr(MsgTypeEnum::SAVE_MESSAGE);

        if (!$messageToSave) {
            return;
        }

        $alreadyExistsMessage = $this->messageModel->messageExists($this->pdo, $messageToSave);

        if ($alreadyExistsMessage) {
            $this->queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message already send");
            return;
        }

        $message = $this->messageModel->save($this->pdo, $messageToSave);

        $this->queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message sent");

        $timestamp = date('d/m/y H:i:s', time());
        echo "- message saved - {$message->id} - {$timestamp} \n";
    }

    private function finish(): void
    {
    }

    public function main(): void
    {
        $this->start();


        do {
            if ($this->processStop) break;

            $this->process();
        } while (true);

        $this->finish();
    }
}


try {
    new Daemon()->main();
} catch (Throwable $e) {
    echo $e->getMessage();
    exit(0);
}



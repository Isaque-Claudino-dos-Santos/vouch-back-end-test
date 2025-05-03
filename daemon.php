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

class Daemon
{
    private bool $processStop = false;

    public function __construct(
        private readonly MsgQueue $queue,
        private readonly PDO      $pdo,
        private readonly Message  $messageModel,
    )
    {
    }

    private function start(): void
    {
        echo "\n --- Daemon Started ---\n";

        echo "\n - Started queue \"{$this->queue->key}\" successfully";

        $this->pdo->exec(CREATE_MESSAGES_TABLE_SCHEMA);

        echo "\n - Create table \"messages\" if not exists table. \n\n";
    }

    private function handleSaveMessage(): void
    {
        $messageToSave = $this->queue->receiveStr(MsgTypeEnum::SAVE_MESSAGE);

        if (!$messageToSave) {
            return;
        }

        if ($this->messageModel->messageExists($messageToSave)) {
            $this->queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message already send");
            return;
        }

        $message = $this->messageModel->save($messageToSave);

        $this->queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message sent");

        $timestamp = date('d/m/y H:i:s', time());

        echo " - message saved - {$message->id} - {$timestamp} \n";
    }


    private function process(): void
    {
        $this->handleSaveMessage();
    }

    private function finish(): void
    {
        echo "\n --- Daemon Finished ---\n";
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
    $env = new Env('.env');
    $queueKey = ($argv[1] ?? null) ?? $this->env->get('QUEUE_KEY');
    $queue = new MsgQueue($queueKey);
    $pdo = new Database($env)->connect();
    $messageModel = new Message($pdo);

    $daemon = new Daemon($queue, $pdo, $messageModel);

    $daemon->main();
} catch (Throwable $e) {
    echo "\n" . $e->getMessage() . "\n";
    exit(0);
}



<?php

require_once __DIR__ . '/autoload.php';

use \Lib\{Database, MsgQueue, Env};
use \Models\Message;
use \Constants\MsgTypeEnum;

const CREATE_TABLE_SQL = <<<SQL
    CREATE TABLE IF NOT EXISTS messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        value VARCHAR(200) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
SQL;


class Daemon
{
    private readonly Env $env;
    private readonly MsgQueue $queue;
    private readonly PDO $pdo;
    private readonly Message $messageModel;
    private bool $processStop = false;

    public function __construct()
    {
        $this->env = new Env('.env');
        $queueKey = $this->env->get('QUEUE_KEY');
        $this->queue = new MsgQueue($queueKey);
        $this->pdo = new Database($this->env)->connect();
        $this->messageModel = new Message();
    }

    private function process(): void
    {
        $message = $this->queue->receiveStr(MsgTypeEnum::SAVE_MESSAGE);

        if (!$message) {
            return;
        }

        $alreadyExistsMessage = $this->messageModel->messageExists($this->pdo, $message);

        if ($alreadyExistsMessage) {
            $this->queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message already send");
        }

        $this->messageModel->save($this->pdo, $message);
        $this->queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message sent");
    }

    public function main(): void
    {
        $this->pdo->exec(CREATE_TABLE_SQL);

        do {
            if ($this->processStop) {
                break;
            };

            $this->process();
        } while (true);
    }
}


try {
    new Daemon()->main();
} catch (Exception $e) {
    echo $e->getMessage();
    exit(0);
}



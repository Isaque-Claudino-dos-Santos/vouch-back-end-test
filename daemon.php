<?php

require_once __DIR__ . '/autoload.php';

use \Lib\{Database, MsgQueue};
use \Models\Message;
use \Constants\MsgTypeEnum;

echo 'Stating Daemon ' . PHP_EOL;

const CREATE_TABLE_SQL = <<<SQL
        CREATE TABLE IF NOT EXISTS messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            value VARCHAR(200) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
SQL;

$queue = new MsgQueue(123);
$pdo = new Database()->connect();

$pdo->exec(CREATE_TABLE_SQL);

while (true) {
    $message = $queue->anywhereReceiveStr($receivedMsgType);

    if ($receivedMsgType === MsgTypeEnum::SAVE_MESSAGE) {
        if (!$message) {
            continue;
        }

        $alreadyExistsMessage = Message::messageExists($pdo, $message);

        if ($alreadyExistsMessage) {
            $queue->sendStr(MsgTypeEnum::FEEDBACK, "Message already send");
            continue;
        }

        Message::save($pdo, $message);
        $queue->sendStr(MsgTypeEnum::FEEDBACK, "Message sent");
    }
}
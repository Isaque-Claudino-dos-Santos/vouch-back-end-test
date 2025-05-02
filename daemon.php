<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/autoload.php';

use \Lib\{Database, MsgQueue, Env};
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

$env = new Env('.env');
$queueKey = $env->get('QUEUE_KEY');
$queue = new MsgQueue($queueKey);
$pdo = new Database($env)->connect();

$pdo->exec(CREATE_TABLE_SQL);

while (true) {
    $message = $queue->receiveStr(MsgTypeEnum::SAVE_MESSAGE, 0);

    if (!$message) {
        continue;
    }

    $alreadyExistsMessage = Message::messageExists($pdo, $message);

    if ($alreadyExistsMessage) {
        $queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message already send");
        continue;
    }

    Message::save($pdo, $message);
    $queue->ifValidSendStr(MsgTypeEnum::FEEDBACK, "Message sent");
}
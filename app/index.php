<?php


require_once '../autoload.php';

use Lib\MsgQueue;
use Constants\MsgTypeEnum;


$queue = new MsgQueue(123);

function indexController(): void
{
    global $queue;

    $message = $queue->receiveStr(MsgTypeEnum::FEEDBACK, MSG_IPC_NOWAIT);

    require __DIR__ . '/form_page.php';
}

function sendController(): void
{
    global $queue;

    $message = $_POST['message'] ?? '';

    $queue->sendStr(MsgTypeEnum::SAVE_MESSAGE, $message);

    header('Location: /');
}


$routes = [
    'GET' => [
        [
            'path' => '/',
            'action' => fn() => indexController()
        ]
    ],
    'POST' => [
        [
            'path' => '/send',
            'action' => fn() => sendController()
        ]
    ]
];


foreach ($routes[$_SERVER['REQUEST_METHOD']] as $route) {
    if ($_SERVER['REQUEST_URI'] !== $route['path']) {
        continue;
    }

    call_user_func($route['action']);
    break;
}
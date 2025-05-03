<?php

namespace app;

use Lib\MsgQueue;
use Constants\MsgTypeEnum;

readonly  class Controller
{
    public function __construct(
        private MsgQueue $queue
    )
    {
    }

    public function sendMessageForm(): void
    {
        $message = $this->queue->receiveStr(MsgTypeEnum::FEEDBACK, MSG_IPC_NOWAIT);

        require __DIR__ . '/form_page.php';
    }


    public function sendMessage(): void
    {
        $message = $_POST['message'] ?? '';

        $this->queue->ifValidSendStr(MsgTypeEnum::SAVE_MESSAGE, $message);

        header('Location: /');
    }
}
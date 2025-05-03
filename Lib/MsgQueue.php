<?php

namespace Lib;

use SysvMessageQueue;

class MsgQueue
{
    public false|SysvMessageQueue $queue;
    private int $maxSize = 1_000;

    public function __construct(
        public readonly int $key,
    )
    {
        $this->queue = msg_get_queue($this->key);
    }

    public function maxSize(int $size): self
    {
        $this->maxSize = $size;
        return $this;
    }

    public function isStrValidToSend(string $str): bool
    {
        return strlen($str) <= $this->maxSize;
    }

    public function getStat(): array
    {
        return msg_stat_queue($this->queue);
    }

    public function sendStr(int $msgType, string $message): void
    {
        msg_send($this->queue, $msgType, $message, false);
    }

    public function ifValidSendStr(int $msgType, string $message): void
    {
        if ($this->isStrValidToSend($message)) {
            $this->sendStr($msgType, $message);
        }
    }

    public function receiveStr(int $msgType, int $flags = 0): string|null
    {
        msg_receive($this->queue, $msgType, $receivedMessageType, $this->maxSize, $message, false, $flags);

        return $message;
    }

    public function anywhereReceiveStr(&$receivedMessageType, int $flags = 0): string|null
    {
        msg_receive($this->queue, 0, $receivedMessageType, $this->maxSize, $message, false, $flags);

        return $message;
    }

    public function sendArray(int $msgType, array|object $data): void
    {
        msg_send($this->queue, $msgType, $data);
    }

    public function receiveArray(int $msgType): array|object|null
    {
        msg_receive($this->queue, $msgType, $receivedMessageType, $this->maxSize, $message);

        return $message;
    }

    public function destroy(): void
    {
        msg_remove_queue($this->queue);
    }

    public function exists(): bool
    {
        return msg_queue_exists($this->key);
    }
}
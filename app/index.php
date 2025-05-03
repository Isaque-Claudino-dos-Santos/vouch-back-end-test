<?php


require_once '../autoload.php';

use Lib\{MsgQueue, Env};
use Constants\MsgTypeEnum;


readonly class Application
{
    public Env $env;
    public int $queueKey;
    public MsgQueue $queue;

    public function __construct()
    {
        $this->env = new Env('../.env');
        $this->queueKey = $this->env->get('QUEUE_KEY');
        $this->queue = new MsgQueue($this->queueKey);
    }

    private function indexController(): void
    {
        $message = $this->queue->receiveStr(MsgTypeEnum::FEEDBACK, MSG_IPC_NOWAIT);

        require __DIR__ . '/form_page.php';
    }


    private function sendController(): void
    {
        $message = $_POST['message'] ?? '';

        $this->queue->ifValidSendStr(MsgTypeEnum::SAVE_MESSAGE, $message);

        header('Location: /');
    }

    private function callRouteActionByRequest(array $routes): void
    {
        foreach ($routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($_SERVER['REQUEST_URI'] !== $route['path']) {
                continue;
            }

            call_user_func($route['action']);
            break;
        }
    }

    public function main(): void
    {

        $getRoutes = [
            [
                'path' => '/',
                'action' => fn() => $this->indexController()
            ]
        ];

        $postRoutes = [
            [
                'path' => '/send',
                'action' => fn() => $this->sendController()
            ]
        ];

        $routes = [
            'GET' => $getRoutes,
            'POST' => $postRoutes
        ];


        $this->callRouteActionByRequest($routes);
    }
}

try {
    new Application()->main();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
    die(0);
}


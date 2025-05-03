<?php


require_once '../autoload.php';

use Lib\{MsgQueue, Env, Route};
use app\Controller;


readonly class Application
{

    public function __construct(
        private Controller $controller,
    )
    {
    }

    private function callRouteRequested(array $routes): void
    {
        /** @var Route $route */
        foreach ($routes as $route) {
            if ($route->wasRequested()) {
                $route->callAction();
                break;
            }
        }
    }

    public function main(): void
    {
        $routes = [
            new Route('/', 'GET', [$this->controller, 'sendMessageForm']),
            new Route('/send', 'POST', [$this->controller, 'sendMessage']),
        ];


        $this->callRouteRequested($routes);
    }
}

try {
    $env = new Env('../.env');
    $queueKey = $env->get('QUEUE_KEY');
    $queue = new MsgQueue($queueKey);
    $controller = new Controller($queue);

    $app = new Application($controller);

    $app->main();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
    die(0);
}


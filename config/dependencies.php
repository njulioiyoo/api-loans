<?php


declare(strict_types=1);

use Illuminate\Database\Capsule\Manager;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

return [
    // Define Monolog logger as a service
    LoggerInterface::class => function () {
        $logger = new Logger('app');

        $fileHandler = new StreamHandler(__DIR__ . '/../logs/app.log', Level::Debug);
        $fileHandler->setFormatter(
            new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message%",
                "Y-m-d H:i:s",
            ),
        );
        $logger->pushHandler($fileHandler);

        return $logger;
    },
    'db' => function () {
        $capsule = new Manager();
        $capsule->addConnection(
            [
                'driver' => 'mysql',
                'host' => 'host.docker.internal',
                'port' => '3336',
                'database' => 'loan_app',
                'username' => 'root',
                'password' => 'mysql-container',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
            ],
        );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },
];

<?php

declare(strict_types=1);

use App\Controllers\{
    LoanController
};
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * Register application routes.
 *
 * @param App $app The Slim application instance.
 *
 * @return void
 */
return function (App $app): void {
    $app->group('/loans', function (RouteCollectorProxy $group) {
        $group->post('/apply', LoanController::class . ':apply');
    });
};

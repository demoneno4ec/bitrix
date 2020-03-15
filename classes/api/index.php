<?php require_once($_SERVER['DOCUMENT_ROOT']
    .'/bitrix/modules/main/include/prolog_before.php'); ?>
<?php
require __DIR__.'/../local/php_interface/vendor/autoload.php';
require_once __DIR__.'/AutoLoader.php';
header('Access-Control-Allow-Origin: *');

use Slim\App;
use Slim\Http\Response;
use Slim\Http\Request;

$settings = require './settings.php';

$app = new App($settings);

$moduleLoadMiddleware = static function ($moduleName = 'iblock') {
    return function (Request $request, Response $response, $next) use (
        $moduleName
    ) {
        if (!CModule::IncludeModule($moduleName)) {
            throw new Exception('Error on loading', 204);
        }

        return $next($request, $response);
    };
};

$app->get('/events/', 'Event:getList')
    ->add($moduleLoadMiddleware('iblock'));

$app->get('/places/', 'Place:getList')
    ->add($moduleLoadMiddleware('iblock'));


try {
    $app->run();
} catch (Throwable $e) {
    $response = new Response();
    $response->withJson([
        'status' => 500,
        'message' => 'Ошибка сервер',
    ], 500, JSON_UNESCAPED_UNICODE);
}

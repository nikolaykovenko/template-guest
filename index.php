<?php
/**
 * @author Nikolay Kovenko <nikolay.kovenko@gmail.com>
 * @date 28.02.15
 */

session_start();
header("Content-Type: text/html; charset=utf-8");
require_once 'vendor/autoload.php';
require_once 'autoload.php';
$loader = new \Example\Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace('simpleCMS', __DIR__ . '/src/simpleCMS');


$config = require_once 'config.php';
$appHelper = simpleCMS\core\ApplicationHelper::getInstance();
$appHelper->setConfig($config);

/**
 * TODO: Инициализацию компонентов нужно вынести в конфиг + использовать dependency injection container. Упрощено.
 */
$appHelper
    ->setComponent('articlesModel', new \simpleCMS\model\Articles())
    ->setComponent('commentsModel', new \simpleCMS\model\Comments());


try {
    $controllerName = \simpleCMS\core\Router::getControllerName($_GET);
    /** @var \simpleCMS\controllers\AController $controller */
    
    if (!class_exists($controllerName)) {
        throw new \simpleCMS\exceptions\Exception404;
    }
    
    $controller = new $controllerName();
    echo $controller->execute();
    
} catch (\simpleCMS\exceptions\Exception404 $e) {
    header("HTTP/1.0 404 Not Found");
    echo $appHelper->render('404.twig');
    
} catch (\simpleCMS\exceptions\UserNotAuthenticatedException $e) {
    echo (new \simpleCMS\controllers\Login())->execute();
    
} catch (Exception $e) {
    echo $e->getMessage();
}

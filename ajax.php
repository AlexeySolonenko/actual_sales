<?php

use csv\http\controllers\HomePageAjaxController;
use csv\http\utils\AjaxResponse;

require_once('./common/common.php');

$pathInfo = ltrim($_SERVER['PATH_INFO'],'/');
$pathparts = explode('/', $pathInfo);

$method = $pathparts[0];
$controller = new HomePageAjaxController();

if (method_exists($controller, $method)) {
    echo $controller->$method();
    exit();
} else {
    AjaxResponse::$errors[] = 'Ajax request not identified';
    echo AjaxResponse::respond();
    exit();
}

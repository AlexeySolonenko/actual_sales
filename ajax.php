<?php

use csv\http\controllers\HomePageAjaxController;
use csv\http\utils\AjaxResponse;

require_once('./common/common.php');

$pathInfo = ltrim($_SERVER['PATH_INFO'], '/');
$pathparts = explode('/', $pathInfo);

$method = $pathparts[0];
$controller = new HomePageAjaxController();
AjaxResponse::$resPayload['debug_req'] = $_REQUEST;
/**
 * @todo to provide an individual sanitation and validation for each call 
 */

foreach ($_REQUEST as $key => $val) {
    $key = filter_var($key, FILTER_SANITIZE_STRING);
    if (!is_array($val)) {
        $_REQUEST[$key] = filter_var($val, FILTER_SANITIZE_STRING);
    }
}


if (method_exists($controller, $method)) {
    echo $controller->$method();
    exit();
} else {
    AjaxResponse::$errors[] = 'Ajax request not identified';
    echo AjaxResponse::respond();
    exit();
}

<?php
require_once('./common/common.php');

use csv\common\Utils;
use csv\http\controllers\HomePageViewController;

try {
    $m = (new HomePageViewController())->getPageModelData();
    ob_start();
    require_once('./http/views/index.php');
    $pageView = ob_get_clean();
    echo $pageView;
} catch (\Exception $e) {
    echo $e->getMessage();
}


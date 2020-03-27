<?php
require_once('./common/common.php');

use csv\common\Utils;
use csv\http\controllers\HomePageViewController;

try{

$m = (new HomePageViewController())->getPageModelData();

}
catch(\Exception $e){
    echo $e->getMessage();
}

//$m['debug'] = Utils::pr($_SERVER,true);
ob_start();
require_once('./http/views/index.php');
$pageView = ob_get_clean();
echo $pageView;

<?php

try {
    /* Autoloaders */
    require_once("./common/csv_autoloader.php");
    $loader = new Psr4AutoloaderClass();
    $loader->register();
    $loader->addNameSpace('csv', './');
    require __DIR__ . '/../vendor/autoload.php';

    /* Initialize MySQL connection */
    $dbConfPathString = file_get_contents('./configs/path_to_db_config.json');
    $dbConfPath = json_decode($dbConfPathString, true)['path'];
    $dbConfString = file_get_contents($dbConfPath);
    $dbConf = json_decode($dbConfString, true);
    $db = new MysqliDb($dbConf);

} catch (\Exception $e) {
    echo $e->getMessage();
}

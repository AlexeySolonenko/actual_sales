<?php


namespace csv\http\controllers;

class HomePageViewController {

    public function getPageModelData(){
        $m = [];

        $assetsPathString = file_get_contents('./configs/assets_path.json');
        $m['assets_path'] = $_SERVER['HTTP_HOST'].'/'.json_decode($assetsPathString,true)['path'];
        $m['csv_url']= preg_replace('/\s/','%20','tab4lioz.beget.tech/TRIAL CSV - CSV.csv');

        return $m;
    }
}
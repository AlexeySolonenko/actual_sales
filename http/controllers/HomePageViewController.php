<?php


namespace csv\http\controllers;

class HomePageViewController {

    public function getPageModelData(){
        $m = [];

        $assetsPathString = file_get_contents('./configs/assets_path.json');
        $m['assets_path'] = $_SERVER['HTTP_HOST'].'/'.json_decode($assetsPathString,true)['path'];
        

        return $m;
    }
}
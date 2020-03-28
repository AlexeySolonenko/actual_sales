<?php


namespace csv\http\controllers;

class HomePageViewController {

    public function getPageModelData(){
        $m = [];

        $assetsPathString = file_get_contents('./configs/assets_path.json');
        if($assetsPathString){
            try{
                $assetsPath = json_decode($assetsPathString,true);
                $assetsPath = $assetsPath['path'];
            } catch (\Exception $e){
                $assetsPath = '';
            }
        }
        $m['assets_path'] = $_SERVER['HTTP_HOST'].'/'.$assetsPath;
        $m['csv_url']= preg_replace('/\s/','%20','tab4lioz.beget.tech/TRIAL CSV - CSV.csv');
        $m['now'] = date('Y-m-d',strtotime('now'));

        return $m;
    }
}
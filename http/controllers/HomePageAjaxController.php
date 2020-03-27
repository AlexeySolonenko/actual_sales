<?php

namespace csv\http\controllers;

use csv\common\Utils;
use csv\http\utils\AjaxResponse;
use MysqliDb;

class HomePageAjaxController
{


    public function getUsers()
    {
        $req = $this->getUsersSanitizeValidate();

        AjaxResponse::$confirms[] = 'test';
        AjaxResponse::$confirms[] = 'test2';
        AjaxResponse::$errors[] = 'test2';
        AjaxResponse::$resPayload['test'] = 'test';
        return AjaxResponse::respond();
    }

    protected function getUsersSanitizeValidate()
    {
    }

    public function createTable()
    {
        try {
            $db = MysqliDb::getInstance();
            $allTables = $this->findAllDbTables();

            /* Get table creation queries and create only those tables, that do not exist */
            $allTablesQueriesString = file_get_contents('./data/create_tables_queries.json');
            $allTablesQueries = json_decode($allTablesQueriesString, true);
            AjaxResponse::$resPayload['debug_tables'] = $allTablesQueries;
            foreach ($allTablesQueries as $tableName => $query) {

                if (in_array($tableName, $allTables)) {
                    AjaxResponse::$errors[] = "Table $tableName already exists, drop the table first to re-create it";
                } else {
                    $query = implode(' ', $query);
                    $db->rawQuery($query);
                    if ($db->getLastErrno() == '0') {
                        AjaxResponse::$confirms[] = "Table $tableName created successfully";
                    } else {
                        AjaxResponse::$errors[] = $db->getLastError();
                    }
                }
            }
        } catch (\Exception $e) {
            AjaxResponse::$errors[] = $e->getMessage();
        }

        return AjaxResponse::respond();
    }

    public function deleteTable()
    {
        $db = MysqliDb::getInstance();
        $allTables = $this->findAllDbTables();

        $allTablesQueriesString = file_get_contents('./data/create_tables_queries.json');
        $allTablesQueries = json_decode($allTablesQueriesString, true);
        foreach ($allTablesQueries as $tableName => $query) {
            /* I don't have access rights to list tables on my hosting, so */
            if (empty($allTables)) {
                $db->rawQuery("DROP TABLE $tableName");
                 if ($db->getLastErrno() == '0') {
                        AjaxResponse::$confirms[] = "Table $tableName deleted successfully";
                    } else {
                        AjaxResponse::$errors[] = $db->getLastError();
                    }
            } else {
                if (in_array($tableName, $allTables)) {
                    $db->rawQuery("DROP TABLE $tableName");
                    if ($db->getLastErrno() == '0') {
                        AjaxResponse::$confirms[] = "Table $tableName deleted successfully";
                    } else {
                        AjaxResponse::$errors[] = $db->getLastError();
                    }
                } else {
                    AjaxResponse::$errors[] = "Table $tableName already deleted.";
                }
            }
        }

        return AjaxResponse::respond();
    }

    protected function findAllDbTables()
    {
        $db = MysqliDb::getInstance();
        $dbConfPathString = file_get_contents('./configs/path_to_db_config.json');
        $dbConfPath = json_decode($dbConfPathString, true)['path'];
        $dbConfString = file_get_contents($dbConfPath);
        $dbConf = json_decode($dbConfString, true);

        $schema = $dbConf['db'];
        $allTables = $db
            ->where('table_schema', $schema)
            ->get('information_schema.tables as t', null, 'table_name');
        $allTables = array_column($allTables, 'TABLE_NAME');

        return $allTables;
    }

    public function uploadCsv()
    {
        //$req = $this->uploadCsv2();
    }
}

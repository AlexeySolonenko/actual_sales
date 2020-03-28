<?php

namespace csv\http\controllers;

use csv\common\Utils;
use csv\http\utils\AjaxResponse;
use DateTime;
use MysqliDb;

class HomePageAjaxController
{


    const COL_CLIENT = 0;
    const COL_DEAL = 1;
    const COL_TIME = 2;
    const COL_ACCEPTED = 3;
    const COL_REFUSED = 4;

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
        $file = $this->getFile();

        if (!empty(AjaxResponse::$errors)) {
            return AjaxResponse::respond();
        }
        $headers = $this->getCsvTableHeadersAndDelimeter($file);
        $totalLines = $this->getTotalLines($file);


        if (!empty(AjaxResponse::$errors)) {
            return AjaxResponse::respond();
        }
        $db = MysqliDb::getInstance();
        try {
            $db->startTransaction();

            $values    = [];
            $batchSize = 100;
            $values = [
                'clients' => [],
                'deals' => [],
                'logs' => [],
            ];
            while ($file->valid() && $file->key() < ($totalLines - 2)) {
                try {

                    $values = $this->sanitizeAndValidateLine($file->current(), $file->key(), $values);
                    // if we upload to DB on every iteration, it takes too long, let's upload in small batches.
                    if ($file->key() % $batchSize == 0 && $file->key() > 1) {
                        $this->processCsvBatchOfLines($values, $headers);
                        AjaxResponse::$resPayload[] = $values;
                        $values = [
                            'clients' => [],
                            'deals' => [],
                            'logs' => [],
                        ];
                    }
                } catch (\Exception $e) {
                    AjaxResponse::$errors[] = $e->getMessage();
                    if ($file->key() % $batchSize == 0 && $file->key() > 1) {
                        $values = [];
                    }
                }
                $file->next();
            }
        } catch (\Exception $e) {
            $db->rollback();
        }
        $db->commit();
        AjaxResponse::$confirms[] = 'File fetched and uploaded successfully';

        return AjaxResponse::respond();
    }

    protected function getFile()
    {
        set_time_limit(0);
        $fp = fopen('./tmp/localfile.tmp', 'w+');
        $ch = curl_init(preg_replace('/\s/', '%20', 'tab4lioz.beget.tech/TRIAL CSV - CSV.csv'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        $file = new \SplFileObject('./tmp/localfile.tmp', 'r');

        if ($file->isExecutable()) {
            AjaxResponse::$errors[] = 'Uploaded file is executable.';
        }

        if ($file->getSize() > '20971520') {
            AjaxResponse::$errors[] = 'Error. File size exceeds limit.';
        }

        return $file;
    }

    public function getCsvTableHeadersAndDelimeter(\SplFileObject $file, $validNumbersOfHeaders = [5], $offset = 0)
    {
        // put file pointer to the very beginning of the file
        $file->rewind();
        // tell PHP to treate a file like CSV with default values
        $file->setFlags(\SplFileObject::READ_CSV);
        // NOTE a preferred CSV file has its headers at row 0, if not, use $offset
        if ($offset !== 0) $file->seek($offset);
        $headers    = $file->current();
        $delimiters = [';', "\t", ':'];
        // some editors erroneously add last empty element, let's remove it, if it is present;
        if (empty(end($headers))) array_pop($headers);

        // a successfull parsing should return: (1) an array, which (2) is of a valid length (count())
        if (is_array($headers) && in_array(count($headers), $validNumbersOfHeaders)) {
            return $headers;
            //
            // if any check fails, then  try to find another delimiter
        } else {
            // in case someone opened a file in a spreadsheet and edited, it might get reformatted, lets' find a delimiter, if required
            foreach ($delimiters as $delimiter) {
                // get headers line
                $file->rewind();
                if ($offset !== 0) $file->seek($offset);
                $file->setCsvControl($delimiter);
                $headers = $file->current();
                // some editors erroneously add last empty element, let's remove it, if it is present;
                if (empty(end($headers))) array_pop($headers);
                if (is_array($headers) && in_array(count($headers), $validNumbersOfHeaders)) {
                    return $headers;
                }
            }
            AjaxResponse::$errors[] = 'Failed to parse headers';
        }
    }

    protected  function getTotalLines(\SplFileObject $file)
    {
        $file->rewind();
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key() + 1;
        // jump to file beginning
        $file->rewind();
        // jump to first line end
        $file->current();
        // jump to second line beginning
        $file->next();

        return $totalLines;
    }


    protected function sanitizeAndValidateLine($line, $lineN, $values)
    {
        if (!empty($line) && $line !== [null]) {
            // excel or libreoffice show row starting from 1, not from 0
            $lineN = $lineN + 1;
            $client = filter_var($line[self::COL_CLIENT], FILTER_SANITIZE_STRING);
            $client = explode('@', $client);
            $clientId = preg_replace('/\s/', '', $client[1]);
            $clientName = preg_replace('/\s/', '', $client[0]);

            $deal = filter_var($line[self::COL_DEAL], FILTER_SANITIZE_STRING);
            $deal = explode('#', $deal);
            $dealType = preg_replace('/\s/', '', $deal[1]);
            $dealLabel = preg_replace('/\s/', '', $deal[0]);

            $time = filter_var($line[self::COL_TIME], FILTER_SANITIZE_STRING);
            $time = \DateTime::createFromFormat('y-m-d H:i', $time);
            $accepted = filter_var($line[self::COL_ACCEPTED], FILTER_SANITIZE_NUMBER_INT);
            $refused = filter_var($line[self::COL_REFUSED], FILTER_SANITIZE_NUMBER_INT);

            if (empty($values['clients'][$clientId])) {
                $values['clients'][$clientId] = $clientName;
            }
            if (empty($values['deals'][$dealType])) {
                $values['deals'][$dealType] = $dealLabel;
            }

            $values['logs'][] = '(' . implode(',', [$clientId, $dealType, $time->getTimestamp(), $accepted, $refused]) . ')';
        }

        return $values;
    }

    protected function processCsvBatchOfLines($values, $headers)
    {
        $db = MysqliDb::getInstance();
        /* $headers are passed as an example. The creation of the table, and of its corresponding columsn, if needed, can be automated */

        $logsQuery = "INSERT INTO deals_log (`client_id`,`deal_type`,`deal_tstamp`,`deal_accepted`,`deal_refused`) VALUES " . implode(',', $values['logs']);
        $db->rawQuery($logsQuery);
        if ($db->getLastErrno() != 0) {
            AjaxResponse::$errors[] = $db->getLastError();
        }

        $clients = [];
        foreach ($values['clients'] as $id => $username) {
            $clients[] = "('$id','$username')";
        }
        $clients = implode(',', $clients);
        $clientsQuery = "INSERT INTO client_list (`client_id`,`username`) VALUES $clients ON DUPLICATE KEY UPDATE client_id = client_id";
        $db->rawQuery($clientsQuery);
        if ($db->getLastErrno() != 0) {
            AjaxResponse::$errors[] = $db->getLastError();
        }

        $deals = [];
        foreach ($values['deals'] as $id => $type) {
            $deals[] = "('$id','$type')";
        }
        $deals = implode(',', $deals);
        $dealsQuery = "INSERT INTO deal_types (`deal_type`,`type_label_en`) VALUES $deals ON DUPLICATE KEY UPDATE deal_type = deal_type";
        $db->rawQuery($dealsQuery);
        if ($db->getLastErrno() != 0) {
            AjaxResponse::$errors[] = $db->getLastError();
        }
    }

    public function getDealsLog()
    {

        try {
            $req = $_REQUEST;
            AjaxResponse::$resPayload['reqest'] = $_REQUEST;
            $from = \DateTime::createFromFormat('Y-m-d H:i:s',$req['from'].'  00:00:00');
            if($from instanceof \DateTime){
                $req['from_tstamp'] = $from->getTimestamp();
            }
            $to = \DateTime::createFromFormat('Y-m-d H:i:s',$req['to'].' 23:59:59');
            if($to instanceof \DateTime){
                $req['to_tstamp'] = $to->getTimestamp();
            }

            $dbData = $this->loadDealsLogData($req);
            $db = MysqliDb::getInstance();
            AjaxResponse::$data = $this->formatDbLogsData($dbData);
            AjaxResponse::$resPayload['query'] = $db->getLastQuery();


            AjaxResponse::$recordsTotal = $db->totalCount;
            AjaxResponse::$recordsFiltered = $db->totalCount;
            AjaxResponse::$draw = $req['draw'];
        } catch (\Exception $e) {
            AjaxResponse::$errors[] = $e->getMessage();
            return AjaxResponse::respond();
        }


        return AjaxResponse::respond();
    }

    protected function loadDealsLogData($req)
    {
        $db = MysqliDb::getInstance();
        $db->join('client_list c', ' c.client_id = l.client_id', 'INNER');
        $db->join('deal_types d', ' d.deal_type = l.deal_type', 'INNER');
        $columns = [
            'c.username as client',
            'd.type_label_en as deal',
            'l.deal_tstamp as timestamp',
            'deal_accepted as accepted',
            'deal_refused as refused',
        ];

        if (!empty($req['order'])) {
            $orderCols = ['username', 'type_label_en', 'deal_tstamp', 'deal_accepted', 'deal_refused'];
            foreach ($req['order'] as $ord) {
                $db->orderBy($orderCols[$ord['column']], $ord['dir']);
            }
        }
        $db->withTotalCount(true);

        if(!empty($req['to_tstamp'])){
            $db->where('l.deal_tstamp',$req['to_tstamp'],'<=');
        }
        if(!empty($req['from_tstamp'])){
            $db->where('l.deal_tstamp',$req['from_tstamp'],'>=');
        }

        if(!empty($req['deal'])){
            $db->where('d.type_label_en','%'.$req['deal'].'%','LIKE');
        }
        if(!empty($req['client'])){
            $db->where('c.username','%'.$req['client'].'%','LIKE');
        }
      

        if (isset($req['start']) && isset($req['length'])) {
            $db->pageLimit = $req['length'];
         
            return $db->paginate('deals_log l', $req['start']+1, $columns);
        } else {
            return $db->get('deals_log l', null, $columns);
        }
    }

    protected function formatDbLogsData($dbData)
    {
        $ret = [];
        foreach ($dbData as $dbRow) {
            $row = $dbRow;
            $row['time'] = [
                'display' => date('Y-m-d H:i', $dbRow['timestamp']),
                'timestamp' => $dbRow['timestamp']
            ];
            unset($row['timestamp']);
            $ret[] = $row;
        }

        return $ret;
    }
}

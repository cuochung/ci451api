<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\General;
use Carbon\Carbon;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-type,Authorization");

class GeneralController extends BaseController
{
    protected $GeneralModel;

    // public function index($dbName, $tableName)
    // {
    //     // $dbName = 'pddpos2023'; // 你可以通过任何逻辑设置这个变量的值
    //     $dbConfig = config('Database')->default;
    //     $dbConfig['database'] = $dbName;

    //     // 创建数据库连接
    //     $db = Database::connect($dbConfig);

    //     // 测试连接和查询
    //     // $query = $db->query('SELECT * FROM member');
    //     // $results = $query->getResultArray();
    //     // $GeneralModel = new General($db,'other');
    //     $GeneralModel = new General($db);
    //     $GeneralModel->setTable($tableName);

    //     $results = $GeneralModel->findAll();
    //     echo '<pre>';
    //     print_r($results);
    //     // 处理结果
    //     // foreach ($results as $row) {
    //     //     echo $row['datalist'];
    //     // }
    // }

    //連結數據庫 $dbName 數據庫名, $tableName 表單名
    function loadDatabase($dbName, $tableName)
    {
        $dbConfig = config('Database')->default;
        $dbConfig['database'] = $dbName;

        // 創建數據庫連結
        $db = Database::connect($dbConfig);
        $this->GeneralModel = new General($db, $tableName);
    }

    /*取得全部資料
        $dbName 數據庫名 
        $tableName 表單名
    */
    function getAll($dbName, $tableName)
    {
        //判斷資料是否包含合法的token
        $isValid = $this->checkToken($dbName);
        // $isValid = true;
        
        if (!$isValid){
            // echo 'Auth not Allow!!';
            $response = [
                'status' => 401,
                'message' => '存取因遺失訂用帳戶密鑰而遭拒。對 API 提出要求時，請務必包含訂用帳戶密鑰。'
            ];

            // 使用 response() 助手返回 JSON 格式響應
            return $this->response->setJSON($response);
        }else{
            $this->loadDatabase($dbName, $tableName);
            $results = $this->GeneralModel->findAll(); //取得所有資料
            return $this->response->setJSON($results);
        }
        
    }

    //判斷token的合法性
    function checkToken($dbName){
        $this->loadDatabase($dbName, 'logined_tokens');

        $token = $this->request->getHeaderLine('Authorization');
        $check_token = $this->GeneralModel->asObject()->where('token', $token)->first();

        if (!$check_token) {
            return false;
        }else{
            return true;
        }
    }


    /*搜尋功能
        datalist 為json格式資料
        $column 為json資料中指定欄位名
        $searchText 為指定搜尋內容,透過like取得資料
    */
    function search($dbName, $tableName)
    {
        $this->loadDatabase($dbName, $tableName);

        $searchText = 'X-Cube'; // 你要查询的名字
        $column = 'order.text'; // 動態欄位名

        $builder = $this->GeneralModel->builder();
        $builder->select('*');
        // $builder->like('JSON_EXTRACT(datalist, "$._name")', $name, 'both', false);
        // $builder->where('JSON_UNQUOTE(JSON_EXTRACT(datalist, datalist, "$.m_name")) LIKE', "%$name%");
        $builder->where('JSON_UNQUOTE(JSON_EXTRACT(datalist, "$.' . $column . '")) LIKE', "%$searchText%");
        // $builder->where('JSON_UNQUOTE(JSON_EXTRACT(datalist, "$.'.$column.'")) LIKE', "%第二版%");
        // $builder->where('JSON_UNQUOTE(JSON_EXTRACT(datalist, "$.m_name")) LIKE', "%張%");

        $query = $builder->get();
        $results = $query->getResultArray();

        echo '<pre>';
        print_r($results);
    }

    function add()
    {
        echo 'run add';
    }

    //新增測試用token
    function  addTestToken($dbName, $tableName)
    {
        $this->loadDatabase($dbName, $tableName);

        //設定 allowedFields
        $this->GeneralModel->setAllowedFields(['name', 'personnel_id', 'token', 'created_at']);

        $token = bin2hex(openssl_random_pseudo_bytes(65));
        $data = [
            'name' => 'John Doe',
            'personnel_id' => '12345',
            'token' =>$token,
            'created_at' => Carbon::now('Asia/Taipei')
        ];

        $insertId = $this->GeneralModel->insert($data);
        if ($insertId) {
            echo "Data inserted successfully. Insert ID: $insertId";
        } else {
            echo "Failed to insert data.";
        }
    }
}

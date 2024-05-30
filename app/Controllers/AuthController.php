<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\General;
use Carbon\Carbon;
use App\Models\User;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-type,Authorization");
// header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

class AuthController extends BaseController
{
    protected $GeneralModel;

    // public function index()
    // {
    //     //
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

    /*  登入判斷 
        需判斷account password 都符合才行
        $tableName 表單名 在後台設定,前台不會顯示任何資料知道是由那個表單去判斷的
    */
    function loginHandler($dbName){
        $this->loadDatabase($dbName,'users');

        //判斷account password 有沒有都符合
        $users = $this->GeneralModel;
        $userInfo = $users
        ->where('account', $this->request->getVar('account'))
        ->where('password', $this->request->getVar('password'))
        ->first();

        if ($userInfo){
            echo 'match';
        }else{
            echo 'no auth';
        }
        exit;
    }
}

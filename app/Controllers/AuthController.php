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
            // echo 'match';
            /*符合登入資格
            1.建構token;->寫到 logined_tokens 表單裡
            2.回傳 state, token
            */
            $token = bin2hex(openssl_random_pseudo_bytes(65));
            $data = [
                'name' => $userInfo['username'],
                'personnel_snkey' => $userInfo['snkey'],
                'token' =>$token,
                'created_at' => Carbon::now('Asia/Taipei')
            ];

            $this->loadDatabase($dbName,'logined_tokens'); //取得 logined_tokens 表單 模型
            $tokens = $this->GeneralModel;
            $this->GeneralModel->setAllowedFields(['name', 'personnel_snkey', 'token', 'created_at']); //設置可存取欄位
            $insertId = $tokens->insert($data); //新增
            $results = [
                'insertId'=>$insertId,
                'state'=>1,
                'token'=>$token,
            ];

        }else{
            // echo 'no auth';
            $results = [
                'state'=>0,
                'message'=>'no auth',
            ];
        }
        
        return $this->response->setJSON($results);
    }

    /*登出*/
    function logout($dbName){
        $this->loadDatabase($dbName,'logined_tokens');

        $token = $this->request->getHeaderLine('Authorization');

        $check_token = $this->GeneralModel->where('token', $token)->first();
        if ($check_token) {
            $this->GeneralModel->where('token',$token)->delete();
            $results = [
                'state'=>1,
                'message'=>'token deleted',
            ];
        }else{
            $results = [
                'state'=>0,
                'message'=>'logout error',
            ];
        }
        return $this->response->setJSON($results);
    }
}

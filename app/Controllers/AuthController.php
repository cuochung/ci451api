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
        $users = $this->GeneralModel; //取得 users 表單模型
        $userInfo = $users
        ->where('account', $this->request->getVar('account'))
        ->where('password', $this->request->getVar('password'))
        ->first();

        if ($userInfo){ //符合登入資格
            $token = bin2hex(openssl_random_pseudo_bytes(65));
            // echo 'match';
            /*
            1.判斷 personnel_snkey 在 logined_tokens 是否存在 token
                不存在-> 新增到 logined_tokens 表單裡
                存在-> 更新 token,登入時間
            2.回傳 state, token
            */

            $this->loadDatabase($dbName,'logined_tokens'); //取得 logined_tokens 表單 模型
            $tokens = $this->GeneralModel; //取得 logined_tokens 表單模型
            $isOldTokenExists = $tokens->asObject()
                ->where('personnel_snkey',$userInfo['snkey'])
                ->first();
            

            $this->GeneralModel->setAllowedFields(['name', 'personnel_snkey', 'token', 'created_at']); //設置可存取欄位
            if (!$isOldTokenExists){ //未存在的 userInfo -> 新增 logined_tokens 資料
                $insertId = $tokens->insert([
                    'name' => $userInfo['username'],
                    'personnel_snkey' => $userInfo['snkey'],
                    'token' =>$token,
                    'created_at' => Carbon::now('Asia/Taipei')
                ]); //新增

                $results = [
                    'state'=>1,
                    'insertId'=>$insertId,
                    'token'=>$token,
                    'active'=>'insert'
                ];
            }else{ //已存在的 userInfo -> 更新 logined_tokens 資料
                $tokens->where('personnel_snkey',$userInfo['snkey'])
                ->set(['token'=>$token,'created_at'=>Carbon::now('Asia/Taipei')])
                ->update();

                $results = [
                    'state'=>1,
                    'token'=>$token,
                    'active'=>'update'
                ];
            }
        }else{ //未符合登入資料
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
            // $this->GeneralModel->where('token',$token)->delete(); //2024.5.31 先不刪除,透過重新登入更換時間
            $results = [
                'state'=>1,
                'message'=>'token not deleted,just logout',
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

<?php

namespace App\Models;

use CodeIgniter\Model;

class General extends Model
{
    protected $table;
    protected $primaryKey       = 'snkey';
    protected $allowedFields    = ['datalist'];

    // protected $allowedFields    = ['name', 'personnel_snkey', 'token', 'created_at'];
    // 自定义的构造函数来接受数据库连接和表名
    public function __construct($db = null, $table = null)
    {
        parent::__construct();

        // 如果提供了数据库连接，则使用它
        if ($db) {
            $this->db = $db;
        }

        // 如果提供了表名，则使用它
        if ($table) {
            $this->table = $table;
        }
    }

    // 方法来设置表名
    public function setTable($table)
    {
        $this->table = $table;
    }
    
    
    // 方法来设置 allowedFields
    public function setAllowedFields(array $fields)
    {
        $this->allowedFields = $fields;
    }
}

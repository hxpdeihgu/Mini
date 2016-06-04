<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/5
 * @Time: 上午9:32
 */
namespace core\db;
use core\base\BaseClass;

class BaseDB extends \core\base\BaseClass{
    /**
     * 数据库句柄
     * @var
     */
    public static $DB;
    /**
     * 数据库编码
     * @var string
     */
    private $charset = 'utf8';
    /**
     * 数据库配置数组
     * @var
     */
    public static $config;
    /**
     * 数据库选择
     * @var string
     */
    private $database = 'master';
    /**
     * 构造函数
     * @param $config
     */
    public function __construct(){
       $this->setConfig();
    }

    /**
     * 初始化配置文件
     * @author hxp
     */
    private function setConfig(){
        self::$config = BaseClass::$config['db'];
    }
    /**
     * 数据库连接
     * @author hxp
     * @param array $dbArray
     * @return [mysqli]
     */
    private function contect($master = 'master'){
        if(!isset(self::$config[$master]['port'])){
            self::$config[$master]['port'] = 3306;
            
        }
        if(self::$DB){
            return self::$DB;
        }
        $link = @mysqli_connect(self::$config[$master]['host'],self::$config[$master]['user'],self::$config[$master]['password'],self::$config[$master]['database'],self::$config[$master]['port']);
        if(mysqli_connect_errno()){
            new \core\base\Error('数据库连接错误,错误原因:'.mysqli_connect_error());
        }else{
            $link->set_charset($this->charset);
            self::$DB = $link;
        }
    }

    /**
     * 设置数据库字符
     * @author hxp
     * @param $char
     */
    public function setCharset($char){
        $this->charset = $char;
    }

    /**
     * 获取数据库字符
     * @author hxp
     * @return [string]
     */
    public function getCharset(){
        return $this->charset;
    }
    /**
     * 设置数据库
     * @author hxp
     * @param $database
     */
    public function setDatabase($database){
        $this->database = $database;
    }

    /**
     * 获取数据库
     * @author hxp
     * @return [string]
     */
    public function getDatabase(){
        return $this->database;
    }

    /**
     * 执行sql语句
     * @author hxp
     */
    public function query($sql){
        $this->contect($this->database);
        return self::$DB->query($sql);
    }

    /**
     * 数据库更新
     * @author hxp
     * @param $sql
     * @return [mixed]
     */
    public function update($sql){
        return $this->query($sql);
    }

    /**
     * 数据库插入
     * @author hxp
     * @param $sql
     * @return [int|string]
     */
    public function insert($sql){
        $this->query($sql);
        return mysqli_insert_id(self::$DB);
    }

    /**
     * 数据库查询
     * @author hxp
     * @param $sql
     * @return [array|bool]
     */
    public function select($sql){
        $result = $this->query($sql);
        if($result){
            $tmp = array();
            while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                $tmp[] = $row;
            }
            return $tmp;
        }
        return false;
    }

    /**
     * 数据库删除
     * @author hxp
     * @param $sql
     * @return [mixed]
     */
    public function delete($sql){
        return $this->query($sql);
    }
}
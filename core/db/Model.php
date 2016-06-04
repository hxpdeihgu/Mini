<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/5
 * @Time: 上午10:40
 */

namespace core\db;


use core\base\Error;

class Model extends BaseDB
{
    /**
     * 数据库表名
     * @var
     */
    private $table;
    /**
     * 数据库条件
     * @var
     */
    private $where;
    /**
     * 数据库排序
     * @var
     */
    private $order;
    /**
     * 数据库限制数量
     * @var
     */
    private $limit;
    /**
     * 数据库分页数量
     * @var
     */
    private $page;
    public function __construct(){
    }

    /**
     * 获取相关参数
     * @author hxp
     * @param $methon
     * @param $params
     * @return [$this]
     */
    public function __call($method,$params){
        switch($method){
            case 'selectDB':
                $this->selectDB($params);
                break;
            case 'table':
                $this->setTable($params);
                break;
            case 'where':
                $this->setWhere($params);
                break;
            case 'order':
                $this->setOrder($params);
                break;
            case 'limit':
                $this->setLimit($params);
                break;
            case 'page':
                $this->setPage($params);
                break;
            default:
                new Error($method.'没有此方法');
        }
        return $this;
    }

    /**
     * 设置数据库
     * @author hxp
     * @param $dbName
     */
    public function selectDB($param){
        if(is_string($param)){
            $this->setDatabase($this->escape($param));
        }else{
            new Error('表名格式错误！');
        }
    }
    public function getSelectDB(){
        return $this->getDatabase();
    }
    /**
     * 数据库条件设置
     * @author hxp
     * @param $param
     */
    public function setWhere(array $param){
        $tmp = '';
        foreach($param as $k=>$v){
            if(is_array($v)){
                $tmp .= $this->arrangeSql($v,$k);
            }elseif(is_string($v)){
                $tmp .= "(".$k.' = "'.$this->escape($v).'")';
            }
        }
//        $sql = implode(' and ',$tmp);
        $this->where = $tmp;
    }
    public function getWhere(){
        return $this->where;
    }

    /**
     * sql数据处理
     * @author hxp
     * @param $v
     * @param $k
     * @return [string]
     */
    private function arrangeSql(&$v,$k){
        switch(trim(strtolower($v[0]))){
            case 'like':
                return '('.$k.' like "'.$this->escape($v[1]).'")';
            break;
            case 'notin':
                if(is_array($v[1])){
                    return '('.$k.' not in ("'.$this->escape(implode(',',$v[1])).'"))';
                }elseif(is_string($v[1])){
                    return '('.$k.' not in ("'.$this->escape($v[1]).'"))';
                }
                break;
            case 'in':
                if(is_array($v[1])){
                    return '('.$k.' in ("'.$this->escape(implode(',',$v[1])).'"))';
                }elseif(is_string($v[1])){
                    return '('.$k.' in ("'.$this->escape($v[1]).'"))';
                }
                break;
        }
    }
    /**
     * 数据库表设置
     * @author hxp
     * @param $param
     */
    public function setTable($param){
        $this->table = $param;
    }
    public function getTable(){
        return $this->table;
    }
    /**
     * 数据库排序设置
     * @author hxp
     * @param $param
     */
    public function setOrder($param){
        $this->order = $param;
    }

    /**
     * 数据库限制设置
     * @author hxp
     * @param $param
     */
    public function setLimit($param){
        $this->limit = $param;
    }

    /**
     * 数据库分页设置
     * @author hxp
     * @param $param
     */
    public function setPage($param){
        $this->page = $param;
    }
    public function getPage(){
        return $this->page;
    }
    /**
     * @author hxp
     * @param $string
     * @return [string]
     */
    private function escape($string){
        return mysql_real_escape_string($string);
    }

}
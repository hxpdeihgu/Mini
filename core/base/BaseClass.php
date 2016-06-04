<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/3
 * @Time: 下午4:19
 */

namespace core\base;


class BaseClass
{
    public static $config;
    public function __construct($config=[]){
        if(is_array($config)){
            self::$config = $config;
        }else{
            $this->config = [];
        }
    }
}
<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/4
 * @Time: 下午5:47
 */
namespace core\base;
class Error{
    public function __construct($message = '',$code = ''){
        exit($message);
    }
}
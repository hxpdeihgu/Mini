<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/12
 * @Time: 下午2:58
 */
include 'Validator.php';
$data=array('id'=>"12345");//初始化字段值
$v = new \Validator\Validator($data);
//后添加字段值

//属性解释，没有默认字段名称

//添加规则数组

//一条规则
//$v->rule('numeric','name','这个不是一个数字');
$v->rules($rules);
print_r($v->validate());//正确返回true，错误返回false
print_r($v->errors());//错误数组
print_r($v->errors('id'));//id错误数组

$v = new \Validator\Validator(array('values' => array(5, 10, 15, 20, 'aa')));
$v = new \Validator\Validator($_POST);
;
$v->rule('integer', 'values','aaa',0,3);
print_r($v->validate());//正确返回true，错误返回false
print_r($v->errors());//错误数组

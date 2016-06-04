<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/3
 * @Time: 下午4:48
 */
function loadClass($className)
{
    $classFile = BASE_PATH.'/'.str_replace('\\', '/', $className) . '.php';
    if (is_file($classFile)) {
        include_once($classFile);
    } else {
        exit($classFile . '文件不存在！');
    }
}
spl_autoload_register('loadClass',true,true );


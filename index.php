<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/3
 * @Time: 下午4:15
 */
define('BASE_SHOP_DEBUG',true);
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
include './core/BaseShop.php';
$config = require('./config/config.php');
(new \core\BaseShop($config))->init();

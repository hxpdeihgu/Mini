<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/3
 * @Time: 下午4:16
 */
namespace core;

use core\base\BaseClass;
use core\base\Error;
use core\db\Model;

require_once 'base/Autoload.php';
class BaseShop extends BaseClass{
    /**
     * 配置项目
     * @var array
     */
    public static $config = [];
    /**
     * 控制器默认命名空间
     * @var string
     */
    private $defaultNamespace = 'controllers';

    /**
     * 构造方法
     * @param array $config
     */
    public function __construct($config=[]){
        parent::__construct($config);
        self::$config = $config;
    }

    /**
     * 控制器运行
     * @author hxp
     * @date
     */
    public function init(){
        $this->runController();
    }
    /**
     * 控制器运行
     * @author hxp
     */
    public function runController(){
        $route = new \core\base\Router();
        if(!class_exists('\\'.$this->getNamespace().'\\'.$route->getController())){
            new Error('\\'.$this->getNamespace().'\\'.$route->getController().'控制器不存在!');
        }
        if(!method_exists('\\'.$this->getNamespace().'\\'.$route->getController(),$route->getAction())){
            new Error($route->getAction().'方法不存在！');
        }
        $class = '\\'.$this->getNamespace().'\\'.$route->getController();
        if (is_object($class)) {
            $class = get_class($class);
        } else {
            $class = ltrim($class, '\\');
        }
        call_user_func(array(new $class, $route->getAction()));
    }
    /**
     * 获取命名空间
     * @author hxp
     * @date
     * @return [string]
     */
    public function getNamespace(){
       return isset(self::$config['controllerNamespace'])? self::$config['controllerNamespace']:$this->defaultNamespace;
    }
    /**
     * 网站基础路径baseUrl
     * @author hxp
     * @return [string]
     */
    public function baseUrl(){
        $currentPath = $_SERVER['SCRIPT_NAME'];
        $pathInfo = pathinfo($currentPath);
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://' ? 'https://' : 'http://';
        return $protocol.$hostName.$pathInfo['dirname']."/";
    }
}

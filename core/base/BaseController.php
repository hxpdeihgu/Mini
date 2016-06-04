<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/4
 * @Time: 上午9:23
 */

namespace core\base;


class BaseController extends View
{
    //视图地址
    private $viewPath = '/view';

    //控制器名称
    public $controller;

    //方法名称
    public $action;

    //参数
    public $params;

    public function __construct(){
        $this->init();
    }

    /**
     * 初始化数据
     * @author hxp
     */
    public function init(){
        $router = new Router();
        $this->action = $router->getAction();
        $this->controller = $router->getController();
        $this->params = $router->getParams();
    }

    /**
     * 视图渲染
     * @author hxp
     * @param string $fileName
     * @param array $parameter
     */
    public function render($fileName,array $parameter=[]){
        $filePateh = $this->getViwPath($fileName);
        $this->getView($filePateh,$parameter);
    }

    /**
     * 地址跳转
     * @author hxp
     * @param $url
     * @param bool|true $replace
     * @param int $code
     */
    public function redirect($url,$replace = false,$code=301){
        if(is_array($url)){
            $tmpUrl = implode('/',$url);
            header('Location '.$tmpUrl,$replace,$code);exit;
        }else{
            header('Location '.$url,$replace,$code);exit;
        }
    }

    /**
     * 获取视图路径
     * @author hxp
     * @param $fileName
     */
    public function getViwPath($fileName){
        if(isset(BaseClass::$config['view'])){
            return $this->viewPath = BaseClass::$config['view'].'/'.$fileName;
        }
        return $this->viewPath;
    }


}
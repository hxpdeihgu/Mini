<?php
/**
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/4
 * @Time: 上午9:32
 */

namespace core\base;


class Router
{
    /**
     * 路由接收变量
     * @var string
     */
    public $requestRouter = '';
    /**
     * 默认控制器
     * @var string
     */
    public $defatltController = 'indexController';
    /**
     * 默认动作
     * @var string
     */
    public $defatltAction = 'indexAction';
    /**
     * 参数变量
     * @var array
     */
    private $params = [];
    /**
     * 控制器后缀
     * @var string
     */

    private $suffixController = 'Controller';
    /**
     * 动作后缀
     * @var string
     */
    private $suffixAction = 'Action';

    /**
     * 默认构造器
     */
    public function __construct(){
        $this->getRouter();
    }

    /**
     * 获取url
     * @author hxp
     * @date
     */
    public function getRouter(){
        if(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])){
            $this->requestRouter = $_SERVER['QUERY_STRING'];
        }elseif(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])){
            $this->requestRouter = $_SERVER['PATH_INFO'];
        }elseif(isset($_SERVER['argv']) && $_SERVER['argc']>1){
            $this->requestRouter = $_SERVER['argv'][1];
        }
    }

    /**
     * 获取控制器
     * @author hxp
     * @date
     * @return [string]
     */
    public function getController(){
        if($this->requestRouter){
            $params = trim($this->requestRouter,'/');
            if(strpos($params,'/')!==false){
                $paramsArray = explode('/',$params);
                return $paramsArray[0].$this->suffixController;
            }else{
                return $params.$this->suffixController;
            }
        }else{
            return $this->defatltController;
        }
    }

    /**
     * 获取动作
     * @author hxp
     * @date
     * @return [string]
     */
    public function getAction(){
        if($this->requestRouter){
            $params = trim($this->requestRouter,'/');
            if(strpos($params,'/')!==false){
                $paramsArray = explode('/',$params);
                //设置参数
                $this->setParams(array_slice($paramsArray,2));
                if(isset($paramsArray[1])){
                    return $paramsArray[1].$this->suffixAction;
                }
            }else{
                return $this->defatltAction;
            }
        }else{
            return $this->defatltAction;
        }
    }

    /**
     * 设置参数
     * @author hxp
     * @param $param
     */
    public function setParams($param){
        if(!is_array($param)) return;
        if(!empty($this->params)){
            $this->params = array_merge($this->params,$param);
        }else{
            $this->params = $param;
        }
    }
    /**
     * 获取参数
     * @author hxp
     */
    public function getParams(){
        return $this->params;
    }
}
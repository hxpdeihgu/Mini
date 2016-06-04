<?php
/**
 * view显示类
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/7
 * @Time: 上午10:25
 */
namespace core\base;



class View extends BaseClass{
    public function __construct(){

    }
    /**
     * 引入模板文件
     * @author hxp
     * @param $file
     * @param array $params
     */
    private function renderFile($_file_,$_params_=[]){
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require($_file_);
        return ob_get_clean();
    }
    public function getView($file,$params=[]){
        if(!is_array($params)){
            $params = [];
        }
        $this->renderFile($file,$params);
    }
}
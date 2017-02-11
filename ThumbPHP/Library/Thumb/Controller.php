<?php

namespace Thumb;

class Controller
{
    //视图实例对象
    protected $view = null;

    /**
     * 取得模板对象实例
     * Controller constructor.
     */
    public function __construct(){
        //实例化视图类
        $this->view = Thumb::instance('Thumb\View');
        //控制器初始化
        if (method_exists($this,'_init')) {
            $this->_init();
        }
    }

    /**
     * 模板显示 调用内置的模板引擎显示方法
     * @return mixed
     */
    public function display($action='', $controller=''){
        return $this->view->display($action, $controller);
    }

    /**
     * 后台往前端传值
     * @param $name
     * @param $value
     * @return mixed
     */
    public function assign($name, $value){
        return $this->view->assign($name, $value);
    }
}
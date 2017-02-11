<?php
namespace Thumb;

class View
{
    //后台数据集合
    private $_data = array();

    /**
     * 后台往前端传值
     */
    public function assign($name,$value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * 渲染结果返回到前端展示，一般做模板引擎，解析html模板
     */
    public function display($action = '',$controller = '')
    {
        if(!empty($this->_data) and $data = $this->_data){
            foreach($data as $key => $_data){
                $$key = $_data;
            }
        }
        $action     = !empty($action)?$action:__ACTION__;
        $controller = !empty($controller)?$controller:__CONTROLLER__;

        include APP_PATH. __MODULE__ . '/View/' . $controller . '/' . $action . HTML_EXT;
    }

}

?>

<?php
/**
 * 分页类
 */

namespace Thumb;

class Page
{
    public $firstRow;               //起始行数
    public $listRows;               //列表每页显示行数
    public $params = array();       //分页跳转时要带的参数
    public $totalRows;              //总行数
    public $totalPages;             //分页总页面数
    public $rollPage = 7;           //分页栏每页显示的数字页数

    private $p = 'p';               //url分页参数
    private $url = '';              //当前url链接
    private $nowPage = 1;

    //分页显示定制
    private $config = array(
        'header' => '<li><a>共%TOTAL_PAGE%页，%TOTAL_ROW%条记录</a></li>',
        'prev'   => '上一页',
        'next'   => '下一页',
        'first'  => '首页',
        'last'   => '末页',
        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%'
    );

    /**
     * 构造函数
     * Page constructor.
     * @param       $totalRows 总的行数
     * @param       $listRows  每页显示记录数
     * @param array $params    每页跳转的参数
     */
    public function __construct($totalRows,$listRows,$params = array())
    {
        C('VAR_PAGE') and $this->p = C('VAR_PAGE');//设置分页参数
        $this->totalRows = $totalRows;
        $this->listRows  = intval($listRows);
        $this->params    = empty($params)?$_GET:$params;
        $this->nowPage   = empty($_GET[$this->p])?1:$_GET[$this->p];
        $this->firstRow  = $listRows*($this->nowPage-1);
    }

    /**
     * 定制分页链接设置
     * @param $name  设置名称
     * @param $value 设置值
     */
    public function setConfig($name,$value)
    {
        if(isset($this->config[$name])){
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param $page 页码
     * @return mixed
     */
    private function url($page)
    {
        return str_replace(urlencode('[PAGE]'),$page,$this->url);
    }

    /**
     * 展示分页
     * @return string
     */
    public function show()
    {
        if(0==$this->totalRows){
            return '';
        }

        //生成url
        $this->generateUrl();
        //生成分页栏
        $pagination = $this->calculatePage();
        return $pagination;
    }

    /**
     * 生成URL
     */
    public function generateUrl()
    {
        $scheme  = is_ssl()?'https://':'http://';
        $host    = $_SERVER['HTTP_HOST'];
        isset($_SERVER['PATH_INFO']) and $proName = $_SERVER['SCRIPT_NAME'];
        !isset($_SERVER['PATH_INFO']) and $scriptName = explode('/',$_SERVER['SCRIPT_NAME']) and $proName = '/'.$scriptName[1];

        if(URL_PATHINFO==C('URL_MODE')){
            $query                  = parsePatnInfoQueryString();
            $params                 = $query['params'];//请求参数
            $this->params           = $params;
            $this->params[$this->p] = '[PAGE]';

            $path   = empty($query['path'])?'':'/'.implode('/',$query['path']);//请求路径
            $params = '?'.http_build_query($this->params);
        }elseif(URL_COMMON==C('URL_MODE')){
            parse_str($_SERVER['QUERY_STRING'],$params);
            $this->params           = $params;
            $this->params[$this->p] = '[PAGE]';

            $path   = NULL;
            $params = '?'.http_build_query($this->params);
        }
        $this->url = $scheme.$host.$proName.$path.$params;
    }

    /**
     * 计算分页信息，并返回分页栏
     * @return string
     */
    public function calculatePage()
    {
        //计算分页信息
        $this->totalPages = ceil($this->totalRows/$this->listRows);//总页面数
        if(!empty($this->totalPages) and $this->nowPage>$this->totalPages){
            $this->nowPage = $this->totalPages;
        }
        //计算分页零时变量
        $now_cool_page      = intval($this->rollPage/2);
        $now_cool_page_ceil = ceil($this->rollPage/2);

        //上一页
        $up_row  = $this->nowPage-1;
        $up_page = $up_row>0?'<li><a href="'.$this->url($up_row).'">'.$this->config['prev'].'</a></li>':
            '<li class="disabled"><a href="'.$this->url($up_row).'">'.$this->config['prev'].'</a></li>';

        //下一页
        $down_row  = $this->nowPage+1;
        $down_page = $down_row>$this->totalPages?'<li class="disabled"><a href="#">'.$this->config['next'].'</a></li>':
            '<li><a href="'.$this->url($down_row).'">'.$this->config['next'].'</a></li>';

        //第一页
        $the_first = '';
        if($this->totalPages>$this->rollPage and ($this->nowPage-$now_cool_page)>=1){
            $the_first = '<li class="previous"><a href="'.$this->url(1).'">'.$this->config['first'].'</a></li>';
        } else {
            $the_first = '<li class="previous disabled"><a href="#">'.$this->config['first'].'</a></li>';
        }

        //最后一页
        $the_end = '';
        if($this->totalPages>$this->rollPage and ($this->nowPage+$now_cool_page)<$this->totalPages){
            $the_end = '<li class="next"><a href="'.$this->url($this->totalPages).'">'.$this->config['last'].'</a></li>';
        } else {
            $the_end = '<li class="next disabled"><a href="#">'.$this->config['last'].'</a></li>';
        }

        //分页数字连接  1 2 3 4 5 6 7 8 9 10 11
        $link_page = '';
        for($i = 1;$i<=$this->rollPage;++$i){
            if(($this->nowPage-$now_cool_page)<=0){
                $page = $i;
            }elseif(($this->nowPage+$now_cool_page-1)>=$this->totalPages){
                $page = $this->totalPages-$this->rollPage+$i;
            }else{
                $page = $this->nowPage-$now_cool_page_ceil+$i;
            }
            if($page>0 and $page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $link_page .= '<li><a href="'.$this->url($page).'">'.$page.'</a></li>';
                }else{
                    break;
                }
            }else{
                if($page>0 and $this->totalPages!=1){
                    $link_page .= '<li class="active"><a>'.$page.'</a></li>';
                }
            }
        }

        //替换分页栏页面内容
        $page_str = str_replace(array(
            '%HEADER%',
            '%NOW_PAGE%',
            '%FIRST%',
            '%UP_PAGE%',
            '%LINK_PAGE%',
            '%DOWN_PAGE%',
            '%END%',
            '%TOTAL_ROW%',
            '%TOTAL_PAGE%'
        ),array(
            $this->config['header'],
            $this->nowPage,
            $the_first,
            $up_page,
            $link_page,
            $down_page,
            $the_end,
            $this->totalRows,
            $this->totalPages
        ),$this->config['theme']);
        return "<div align='center'><ul class=\"pager\">{$page_str}</ul></div>";
    }
}
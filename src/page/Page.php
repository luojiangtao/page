<?php

namespace luojiangtao\page;

/**
 * 分页
 * Class Page
 * @package luojiangtao\page
 */
class Page
{
    // 总共有多少条数据
    private $count;
    // 每页显示多少条，limit 语句用的第二个数 limit 2,5
    public $page_size;
    // 当前url,已经去掉  &p=1 参数
    private $url;
    // 用户分页时，limit 语句用的第一个数 limit 2,5
    public $limit_page;
    // 当前页
    private $now_page;
    // 上一页
    private $pre_page;
    // 下一页
    private $next_page;
    // 总共多少页
    private $totol_page;
    // limit 从多少行开始搜索
    public $start_row;
    // 数子开始页
    private $number_start;
    // 数字结尾页
    private $number_end;
    // 显示多少个数字分页 如$number_show=2那么页面上显示就是[首页] [上页] 1 2 3 4 5 [下页] [尾页]
    private $number_show=2;

    /**
     * 构造方法
     * Page constructor.
     * @param integer $count [数据总条数]
     * @param integer $page_size [每页多少条]
     */
    public function __construct($count, $page_size)
    {
        if(!$count){
            $this->count = 0;
            $this->start_row = 0;
            $this->page_size = 0;
            return ;
        }
        if($count<=$page_size){
            $page_size=$count;
        }
        $this->count      = $count;
        $this->page_size  = $page_size;

        $this->totol_page = ceil($this->count / $this->page_size);
        $this->now_page   = isset($_GET['p']) ? $_GET['p'] : 1;
        $this->pre_page   = $this->now_page - 1;
        $this->start_row  = ($this->now_page - 1) * $page_size;
        // 用于分页sql用  M('article')->limit($Page->now_page-1.','.$Page->listRows)->select();
        $this->limit_page = $this->now_page - 1;
        $this->next_page  = $this->now_page + 1;
        $this->url        = $_SERVER['REQUEST_URI'];
//        去掉 p=数字 参数
        $this->url        = preg_replace("/p=\d+&?/i", '', $this->url);
        $this->url        = rtrim($this->url,'&');
        $this->url        = rtrim($this->url,'?');

        $this->number_start = $this->now_page - $this->number_show;
        $this->number_end = $this->now_page + $this->number_show;
        if($this->number_start<1){
            $this->number_end = $this->now_page + 3;
            if($this->number_end>$this->totol_page){
                $this->number_end=$this->totol_page;
            }
            $this->number_start=1;
        }

        if($this->number_end>$this->totol_page){
            $this->number_start = $this->now_page - 3;
            $this->number_end=$this->totol_page;

            if($this->number_start<1){
                $this->number_start=1;
            }
        }
    }

    /**
     * 创建分页url参数
     * @return string
     */
    public function createParam()
    {
        if(strpos($this->url,'?')){
            return '&p=';
        }else{
            return '?p=';
        }
    }

    /**
     * 获取分页html代码
     * @return string [分页html代码]
     */
    public function show()
    {
        if(!$this->count){
            return ;
        }
        $page_html = '';
        // 首页
        if ($this->now_page > 1) {
            $page_html .= "<a class='page' href='" . $this->url . "'>首页</a>";
        }else{
            $page_html .= "<a class='page-disable'>首页</a>";
        }

        // 上一页
        if ($this->pre_page > 0) {
            $pre_page_url = $this->url . $this->createParam() . $this->pre_page;
            $page_html .= "<a class='page' href='" . $pre_page_url . "'>«</a>";
        }else{
            $page_html .= "<a class='page-disable'>«</a>";
        }

        for($i=$this->number_start;$i<=$this->number_end;$i++){
            if($i==$this->now_page){
                $pre_page_url = $this->url . $this->createParam() . $i;
                $page_html .= "<a class='page now-page' href='" . $pre_page_url . "'>$i</a>";
            }else{
                $pre_page_url = $this->url . $this->createParam() . $i;
                $page_html .= "<a class='page' href='" . $pre_page_url . "'>$i</a>";
            }
        }

        // 下一页
        if ($this->next_page <= $this->totol_page) {
            $next_page_url = $this->url . $this->createParam() . $this->next_page;
            $page_html .= "<a class='page' href='" . $next_page_url . "'>»</a>";
        }else{
            $page_html .= "<a class='page-disable'>»</a>";
        }

        // 尾页
        if ($this->now_page < $this->totol_page) {
            $last_page = $this->url . $this->createParam() . $this->totol_page;
            $page_html .= "<a class='page last-page' href='" . $last_page . "'>尾页</a>";
        }else{
            $page_html .= "<a class='last-page page-disable' >尾页</a>";
        }

        // $page_html .= " <span class='page'>共" . $this->totol_page . "页 " . $this->count . "条数据</span>";

        return $page_html;
    }
}

//测试用
//var_dump($_GET);
//$page = new Page(100, 10);
//echo $page->show();
//var_dump($_SERVER);
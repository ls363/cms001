<?php


namespace App\Utils;

use App\Enums\PageEnums;

/**
 * Class PagerBar
 * 静态页面生成专用的分页条
 *
 * @package App\Libraries\HtmlMaker
 * @author lichunguang 153102250@qq.com
 * @since 2022/5/2 下午11:14
 */
class PageBar
{

    private static $defaultTemplate = '<div class="page_bar">第{page_option}页 {first}{prev}{page_list}{next}{last} <span class="special">{page_size}</span> {unit}每页 共 <span class="special">{total_records}</span> {unit} <span class="special">{total_pages}</span> 页</div>';
    private static $text = ['first' => '首页', 'prev' => '上一页', 'next' => '下一页', 'last' => '末页'];

    private $page = 0;
    private $pageSize = 0;
    private $totalRecords = 0;
    private $totalPages = 0;
    private $pageUrl = '';
    private $template = '';
    private $numListSize = 6; //页码显示个数
    private $unit = ''; //数量的单位， 个，条，篇
    private $buttonClass = "";
    private $numberClass = "";
    private $numberActiveClass = "";
    private $numberActiveHtml = "span";

    public function getButtonClass()
    {
        if(empty($this->buttonClass)){
            $this->buttonClass = "page-item page-link";
        }
        return $this->buttonClass;
    }

    public function getNumberClass()
    {
        if(empty($this->numberClass)){
            $this->numberClass = "page-item page-link";
        }
        return $this->numberClass;
    }

    public function getNumberActiveClass()
    {
        if(empty($this->numberActiveClass)){
            $this->numberActiveClass = "page-num-current";
        }
        return $this->numberActiveClass;
    }

    public function getNumberActiveHtml()
    {
        if(empty($this->numberActiveHtml)){
            $this->numberActiveHtml = "span";
        }
        return $this->numberActiveHtml;
    }

    /**
     * @param  $button_class mixed
     */
    public function setButtonClass($button_class)
    {
        $this->buttonClass = $button_class;
    }

    /**
     * @param  $button_class mixed
     */
    public function setNumberClass($button_class)
    {
        $this->numberClass = $button_class;
    }

    /**
     * @param  $button_class mixed
     */
    public function setNumberActiveClass($button_class)
    {
        $this->numberActiveClass = $button_class;
    }

    /**
     * @param  $active_html mixed
     */
    public function setNumberActiveHtml($active_html)
    {
        $this->numberActiveHtml = $active_html;
    }




    /**
     * @return mixed
     */
    public function getTotalPages()
    {
        if ($this->totalPages == 0) { //无法区分没有数据，还是没有计算
            $this->totalPages = ceil($this->getTotalRecords() / $this->getPageSize());
        }
        return $this->totalPages;
    }

    /**
     * @param  mixed  $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        if ($this->page == 0) {
            $page = request()->input('page', PageEnums::DEFAULT_PAGE);
            $this->setPage($page);
        }
        $totalPage = $this->getTotalPages();
        if ($this->page > $totalPage) {
            $this->setPage($totalPage);
        }
        return $this->page;
    }

    /**
     * @param  mixed  $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->pageSize == 0 ? PageEnums::PAGE_SIZE : $this->pageSize;
    }

    /**
     * @param  mixed  $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return mixed
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     * @param  mixed  $totalRecords
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    /**
     * @return mixed
     */
    public function getPageUrl()
    {
        if (empty($this->pageUrl)) {
            $url = request()->uri();
            $args = request()->all();
            if (isset($args['page'])) {
                unset($args['page']);
            }
            if (empty($args)) {//没有查询参数的情况
                $url .= '?page=';
            } else {
                $url .= '?' . http_build_query($args) . '&page=';
            }
            //赋值
            $this->setPageUrl($url);
        }

        return $this->pageUrl;
    }

    /**
     * @param  mixed  $pageUrl
     */
    public function setPageUrl($pageUrl)
    {
        $this->pageUrl = $pageUrl;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template == '' ? self:: $defaultTemplate : $this->template;
    }

    /**
     * @param  string  $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    /**
     * @return int
     */
    public function getNumListSize(): int
    {
        return $this->numListSize == 0 ? 10 : $this->numListSize;
    }

    /**
     * @param  int  $numListSize
     */
    public function setNumListSize(int $numListSize)
    {
        $this->numListSize = $numListSize;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit == '' ? '条' : $this->unit;
    }

    /**
     * @param  string  $unit
     */
    public function setUnit(string $unit)
    {
        $this->unit = $unit;
    }


    /**
     * 根据page获取链接
     *
     * @param $page
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/12 下午8:06
     */
    public function getLinkUrl($page){
        $url = $this->getPageUrl();
        //如果动态链接, 直接跟上page
        if(strpos($url, 'page=')){
            return $url . $page;
        }
        if($page == 1){
            return $url;
        }
        $url .= ('_'. $page);
        return $url;
    }

    public function getPageBar()
    {
        if ($this->getTotalRecords() == 0) {
            return '';
        }
        if($this->getTotalPages() == 1){
            return '';
        }
        $html = $this->getTemplate();
        $data = self::$text;
        $page = $this->getPage();
        $data['page'] = $this->getPage();
        $data['page_size'] = $this->pageSize = $this->getPageSize();
        $data['total_records'] = $this->getTotalRecords();
        $data['total_pages'] = $this->getTotalPages();
        $data['unit'] = $this->getUnit();
        $data['page_list'] = $this->getNumList();
        $data['page_option'] = $this->getSelect();

        $totalPage = $this->getTotalPages();

        $css = 'class="'. $this->getButtonClass() .'"';
        //首页的处理
        if ($page == 1) {
            $data['first'] = "<span {$css}>". self::$text['first'] . '</span>';
            $data['first_url'] = '#';
        } else {
            $data['first_url'] =  $this->getLinkUrl(1);
            $data['first'] = '<a '. $css .' href="' .  $data['first_url'] . '">' . self::$text['first'] . '</a>';

        }
        //上一页的处理
        if ($page > 1 && $totalPage > 1) {
            $data['prev_url'] = $this->getLinkUrl($page - 1);
            $data['prev'] = '<a '. $css .' href="' . $data['prev_url'] . '">' . self::$text['prev'] . '</a>';
        } else {
            $data['prev_url'] = '#';
            $data['prev'] = "<span {$css}>" . self::$text['prev'] . '</span>';
        }
        //下一页的处理
        if ($page < $totalPage && $totalPage > 1) {
            $data['next_url'] = $this->getLinkUrl($page + 1);
            $data['next'] = '<a '. $css .' href="' . $data['next_url'] . '">' . self::$text['next'] . '</a>';
        } else {
            $data['next_url'] = '#';
            $data['next'] = "<span {$css}>" . self::$text['next'] . '</span>';
        }
        //尾页的处理
        if ($page == $totalPage) {
            $data['last_url'] = '#';
            $data['last'] = "<span {$css}>" . self::$text['last'] . '</span>';
        } else {
            $data['last_url'] = $this->getLinkUrl($totalPage);
            $data['last'] = '<a '. $css .'href="' . $data['last_url'] . '">' . self::$text['last'] . '</a>';
        }
        foreach ($data as $k => $v) {
            $html = str_replace('{' . $k . '}', $v, $html);
        }
        return $html;
    }

    /**
     * 处理页码列表 ...123456...
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/2 下午11:43
     */
    public function getNumList()
    {
        $page = $this->getPage();
        $numListSize = $this->getNumListSize();
        $iListCount = intval($page / $numListSize); //页数要乘以几个10
        $pageUrl = $this->getPageUrl();
        $sPageList = '';
        $css = 'class="'. $this->getButtonClass() .'"';
        $css_active = 'class="'. $this->getButtonClass() .' '. $this->getNumberActiveClass() .'"';
        $tag = $this->getNumberActiveHtml();
        if ($page == $numListSize * $iListCount) {
            for ($i = 1; $i <= $numListSize; $i++) {
                $sListItem = $i + $numListSize * ($iListCount - 1);    //第几页
                if ($sListItem == $page) {
                    $sPageList .= "<$tag $css_active> $sListItem </$tag> ";
                } else {
                    $sPageList .= "<a $css href=\"" . $this->getLinkUrl($sListItem) . "\">" . $sListItem . "</a>" . ' ';
                }
                if ($sListItem >= $this->getTotalPages()) {
                    break;
                }
            }
        } else {
            for ($i = 1; $i <= $numListSize; $i++) {
                $sListItem = $i + $numListSize * ($iListCount);    //第几页
                if ($sListItem == $page) {
                    $sPageList .= "<$tag $css_active> $sListItem </$tag> ";
                } else {
                    $sPageList .= "<a $css href=\"" . $this->getLinkUrl($sListItem) . "\">" . $sListItem . "</a>" . ' ';
                }
                if ($sListItem >= $this->getTotalPages()) {
                    break;
                }
            }
        }
        return $sPageList;
    }

    /**
     * 处理跳转下拉列表框
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/2 下午11:44
     */
    public function getSelect()
    {
        $page = $this->getPage();
        $totalPages = $this->getTotalPages();
        $sPageOption = '<select lay-ignore onChange="window.navigate(this.value)" name="select2">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $sPageOption .= '<option value="' . $this->getLinkUrl($i) . '"';
            if ($i == $page) {
                $sPageOption .= ' selected="selected"';
            }
            $sPageOption .= ">" . $i . "</option>";
        }
        $sPageOption .= "</select>";
        return $sPageOption;
    }

    /**
     * 模板解析专用,及后台展示
     *
     * @param int $totalRecords
     * @param  int  $pageSize
     * @param  int  $page
     * @return string|string[]
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/18 下午4:48
     */
    public function show(int $totalRecords, int $pageSize = PageEnums::PAGE_SIZE, int $page = PageEnums::DEFAULT_PAGE)
    {
        $this->setTotalRecords($totalRecords);
        $this->setPageSize($pageSize);
        $this->setPage($page);
        return $this->getPageBar();
    }

}


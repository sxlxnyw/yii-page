<?php

namespace ly\lib\wigdets;


use Yii;
use yii\helpers\Html;
use yii\helpers\Json;

class Pager  extends \yii\widgets\LinkPager{

    public $maxButtonCount = 5;
    /**
     * {pageButtons} {customPage}
     */
    public $template = '{pageButtons}{customPage}';

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }
        echo $this->renderPageContent();
    }

    protected function renderPageContent(){
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
            $name = $matches[1];
            if('customPage' == $name){
                return $this->renderCustomPage();
            }
            else if('pageButtons' == $name){
                return $this->renderPageButtons();
            }
            return "";
        }, $this->template);
    }

    protected function renderCustomPage()
    {
        $pageSize = $this->pagination->getPageSize();
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $currentPage = $this->pagination->getPage();
        $goPage = $currentPage + 2;
        if($goPage>$pageCount)
        {
            $goPage = $pageCount;
        }
        $goHtml = <<<goHtml
            <div class="form" style="float: left; color: #999; margin-left: 10px; font-size: 12px;line-height:34px">
             <span class="text">|</span>
             <span class="text">当页显示</span>
             <select aria-controls="datatable_orders" class="form-control input-xsmall input-sm input-inline set_page_size">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="150">150</option>
                <option value="1000">所有数据</option>
             </select>
             <span class="text">条数据</span>
             
             <span class="text">|</span>
             <span class="text">共 {$pageCount} 页</span>
             <span class="text">到第</span>
             <input class="input" type="number" value="{$goPage}" min="1" max="{$pageCount}" aria-label="页码输入框" style="text-align: center; height: 32px; border: 1px solid #ddd;
border-radius: 4px; margin-top: 5px; width: 46px;">
             <span class="text">页</span>
             <span class="btn go-page" role="button" tabindex="0" style="border: solid 1px #337ab7; color: #fff; background-color: #337ab7; padding: 0px; height: 32px; width: 46px; line-height: 30px;">确定</span>
             
            </div>
goHtml;
        $buttons[] = $goHtml;
        $pageLink = $this->pagination->createUrl(false,$pageSize);
        $currentPageLink = $this->pagination->createUrl($currentPage,1);
        $goJs = <<<goJs
            $(".set_page_size").val("{$pageSize}");
            $(".set_page_size").on('change',function(){
                var pageLink = "{$currentPageLink}";
                var pageSize = $(this).children('option:selected').val()
                pageLink = pageLink.replace("per-page=1", "per-page="+pageSize);
                window.location.href=pageLink;
            });
            
            $(".go-page").on("click", function () {
             var _this = $(this),
              _pageInput = _this.siblings("input"),
              goPage = _pageInput.val(),
              pageLink = "{$pageLink}";
              pageLink = pageLink.replace("page=1", "page="+goPage);
             if (goPage >= 1 && goPage <= {$pageCount}) {
              window.location.href=pageLink;
             } else {
              _pageInput.css('border','2px solid #e82d2d').focus();
             }
            });
goJs;
        $this->view->registerJs($goJs);

        return Html::tag('ul', implode("\n", $buttons), $this->options);
    }
}

# 文章模型的标签

> 这是loop标签的使用实例，文章是重要的模型

#### 文章内容，查询一篇文章
> :id 表示动态参数，loop标签都是通过":" 或者 "$" 前辍来获取动态参数。
```
<loop table_name="article" global="1" id=":id" record_num="1">
    <h2 class="text-center fs-sm-28 fs-20 mt-3">{$title}</h2>
    <div class="text-center border-bottom text-secondary pb-2 mb-3">
        时间：{$created_at}&nbsp;&nbsp;&nbsp;访问量：<script src="{#site_domain#}/home/hits/addHits?id={$id}&model_id={request.model_id}"></script>
    </div>
    <div class="content mb-3" style="min-height: 300px;">
        {$content}
    </div>

</loop>
```

#### 内容页的global标签
> 因为文章内容标签，有global=1，所以global的内容为文章表的一条记录，所可以global标签的字段可以使用文章表的字段。
```
<html lang="zh">
<head>
    <meta charset="utf-8">
    <title>{global.seo_title}</title>
    <meta name="keywords" content="{global.seo_keywords}">
    <meta name="description" content="{global.seo_description}">
```


### 上一篇文章
```
<loop table_name="article" loop_type="previous" id=":id" record_num="1" order_by="id|desc">
<p>上一篇: <a href="{$content_url}">{$title}</a></p>
</loop>
```

### 下一篇文章
```
<loop table_name="article" loop_type="next" id=":id" record_num="1" order_by="id|asc">
<p>下一篇: <a href="{$content_url}">{$title}</a></p>
</loop>
```

### 文章列表

```
 <loop table_name="article" include_child="1" class_id=":class_id" is_page="1" page_size="10" page=":page" order_by="id|desc">
    <li class="lh-3 border-bottom-dashed">
        <i class="fa fa-gg"></i>
        <a href="{$content_url}">
            <span>{index}</span><span class="d-none d-md-inline">{$title,strCutLen,16}</span><!-- PC端 -->
            <span class="d-inline d-md-none">{$title,strCutLen,11}</span><!-- 移动端 -->
            {if $item['is_top'] == 1 }
            <span class="badge badge-danger">置顶</span>
            {/if}


            <span class="float-right">{$created_at}</span>
        </a>
    </li>
</loop>
```
> {i} 显示序号 从1开始 {index} 也是序号，从0开始。{if}{/if}标签中，只能使用$index。如{if $index == 5}{/if}

> {if $item['is_top'] == 1 } if中的变量只能使用$item['is_top'], 不能直接使用 $is_top。
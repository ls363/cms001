# 位置标标

> 用于显示当前位置，显示某个分类的具体路径。

```
    <!-- 当前位置 -->
    <div class="text-right position mb-3 d-none d-md-block">
    当前位置：<a href="/">首页</a>
    <position>><a href="{$list_url}">{$title}</a></position>
    </div>
```
**参数说明**

| 标签         | 备注   |
|------------|------|
| {list_url} | 栏目地址 |
| {title}    | 栏目名称 |
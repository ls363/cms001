# 栏目标签

### 栏目列表
```
 <ul class="navbar-nav">
    <li class="nav-item ">
        <a class="nav-link" href="{#site_domain#}" >首页</a>
    </li>
    <loop name="classify" parent_id="0" order_by="sort|asc" cache="1">
    <li class="nav-item ">
        <a class="nav-link" href="{$list_url}">{$title}</a>
    </li>
    </loop>
</ul>
```

**loop参数说明**

| 标签                  | 备注         |
|---------------------|------------|
| parent_id="0"       | 只展示一级分类    |
| cache="1"           | 缓存栏目数据     |
| order_by="sort asc" | 按照排序字段升序排列 |

**参数说明**

| 标签         | 备注      |
|------------|---------|
| {list_url} | 栏目地址    |
| {title}    | 栏目名称    |


### 二级菜单

>下面这种方式, 在查询子菜单的时候，仅查询两次数据库，显著提升了效率
 
```
<loop table_name="classify" parent_id="0" state="1" loop_type="main_menu" cache="1">
    <h1>{$title}</h1>
    <nextloop loop_type="sub_menu">
        <p>[$title]</p>
    </nextloop>
</loop>
```
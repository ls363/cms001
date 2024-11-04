# 循环标签

#### \<loop 参数集> 循环体 \</loop>
#### \<nextloop 参数集> 循环体 \</nextloop>

#### 参数集的格式   $id这种只会出现在nextloop标签中，一般的数据展示，很少出现三层嵌套的情况。
```
class_id="1" 固定参数
id=":id"  URL参数
id="$id", loop标签返回的结果，仅出现在nextloop中。
:id 表标用的是请求参数，URL中有类似于?id=1这种。
```

### 条件参数
```
生成查询SQL的WHERE参数
table_name 不带前辍的内容表，如表为gs_article, loop中 table_name="article"
id 内容ID，详情页，id=":id", 即可。
class_id 内容分类ID，列表页 class_id=":class_id", 固定的就用 class_id="指定的ID"
model_id 内容模型ID，model_id=":model_id", 主要用于评论页。
before_day="5" 查询最近5天内添加的数据 
```

### 控制参数
```
global="1" 仅用于单条数据的loop, 将查询出来的结果放到global变量中，通过{global.字段名}访问。
cache="1" 缓存这个loop的数据, 避免二次查询，如果是有动态参数（:class_id 或者 $class_id）不要使用。
include_child="1"  是否要查询子分类，配合class_id使用，有些列表页想读取几个分类。 
loop_type="previous" 上一个内容
loop_type="next" 下一个内容
loop_type="position" 位置标签，这个选项可能不在用了，现在有专用的你的位置标签了。
loop_type="related" 相关内容，本分类的内容
loop_type="data" 指定的数据源，仅用于nextloop, data为数据源字段。
 <nextloop loop_type="data" data="slideList">
<div class="swiper-slide">
    <img src="[$url_pre$]">
</div>
</nextloop>
```

### 排序参数
```
order_by 排序 
order_by="id" 默认倒序排列
order_by="id|desc" ID倒序排列
order_by="class_id,score" 按照class_id倒序 再按score倒序
order_by="class_id|desc,score|asc" 指定每个字段的排序方式
```


### 表格参数
```
//表格参数，主要用于自动生成表格，这个版本暂不使用
col 表格的列数
table_width 表格的宽度
td_align 单元格的对齐方式
table_class 表格的CSS
```
### 分页参数
```
//分页参数，非常重要
is_page 是否需要分页
page 第几页 通常都是 page=":page"
page_size 每页数量 分页的时候使用
record_num 每次读取的记录数，非分页的时候用
```

### 搜索参数
```
//搜索参数
tag 类似于Blog中的标签。
keywords 关键字，从标题与摘要中检索数据
search_type 搜索字段，如title, intro, 仅支持单个字段
search_text 搜索的内容，配合search_type使用
```
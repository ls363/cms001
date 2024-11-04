# 分页标签

> 为了方便使用，特的简化了分页条的展示，

```
<pagebar button_class="" number_class="" number_active_class="" number_active_html="">{first}{prev}{page_list}{next}{last}</pagebar> 分页标签
```

**属性说明**

| 标签                  | 备注                       |
|---------------------|--------------------------|
| button_class        | 首页 上页 下页 尾页 的 CSS        |
| number_class        | 数字按钮 的 CSS               |
| number_active_class | 数字按钮当前页的CSS              |
 | number_active_html  | 当前页的展示 \<span\> 还是 \<a\> |
**参数说明**

| 标签            | 备注             |
|---------------|----------------|
| {first}       | 首页             |
| {prev}        | 上一页            |
| {next}        | 下一页            |
| {last}        | 尾页             |
| {page_list}   | 1 2 3 4 5 页码列表 |
| {page_option} | 页码的下拉框         |
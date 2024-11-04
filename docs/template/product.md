# 产品模型的标签

> 产品内容页标签，这里展示了带轮播图产品内容页的标签。
```
<loop table_name="product" id=":id" record_num="1" global="1">
        
        <div class="swiper-container">
           <div class="swiper-wrapper" >
               //这里展示nextloop标签的用法slideList 是一个数组，[['url'=>"大图", "url_pre"=>"小图"}]
               <nextloop loop_type="data" data="slideList">
               <div class="swiper-slide">
                   <img src="[$url$]">
               </div>
               </nextloop>
           </div>
       </div>

    <h2 class="border-bottom pb-2 fs-sm-28 fs-20">{$title}</h2>
    
    //以下三行的字段，是通过模型扩展生成的
    <div class="text-secondary my-3 border-bottom-dashed lh-3">
产品类型：{$type}
    </div>

    <div class="text-secondary my-3 border-bottom-dashed lh-3">
产品颜色：{$color}
</div>
     <div class="text-secondary my-3 border-bottom-dashed lh-3">
产品价格：￥{$price}
    </div>

</loop>
```

> 其它的一些用法，与文章模块类似
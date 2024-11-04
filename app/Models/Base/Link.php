<?php
namespace App\Models\Base;

use App\Models\LinkCategoryModel;

class Link extends Model {

    /**
     * 关联分类
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/8/24 下午11:00
     */
    public function category(){
        //链接属于分类 （分类表，链接表中分类的字段，分类表的ID）
        return $this->belongsTo(LinkCategoryModel::class, 'category_id', 'id');
    }

}
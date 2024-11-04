<?php

namespace App\Utils;

/**
 * Class TreeUtils
 * 树形结构转换
 *
 * @package App\Utils
 * @author lichunguang 153102250@qq.com
 * @since 2022/5/7 下午4:47
 */
class TreeUtils
{
    /**
     * 数据转为树形结构
     * @param  array  $arr
     * @param  int  $pid
     * @param  string  $pidName
     * @param  string  $idName
     * @param  string  $childName
     * @return array
     */
    public static function getTreeArrayByListArray(
        array $arr,
        $pid = 0,
        $pidName = 'parent_id',
        $idName = 'id',
        $childName = 'children'
    ) {
        $tree = array();
        if (empty($arr)) {
            return $arr;
        }
        foreach ($arr as $key => $item) {
            if ($item[$pidName] == $pid) {
                $item[$childName] = self::getTreeArrayByListArray($arr, $item[$idName], $pidName, $idName, $childName);
                if (!$item[$childName]) {
                    unset($item[$childName]);
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }

    /**
     * 树形数组转一维数组
     * @param  array  $arr
     * @param  string  $childName
     * @return array
     */
    public static function getListArrayByTreeArray(array $arr, $childName = 'children')
    {
        $list = array();

        foreach ($arr as $item) {
            $tem = [];
            if ($item[$childName]) {
                $tem = self::getListArrayByTreeArray($item[$childName]);
                unset($item['children']);
            }
            $list[] = $item;
            $list = array_merge($list, $tem);
        }
        return $list;
    }

}
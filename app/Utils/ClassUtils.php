<?php

namespace App\Utils;

class ClassUtils
{
    private $data = [];

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  array  $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function __construct()
    {
    }

    /**
     * 显示下拉框
     *
     * @param $name
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/5/7 下午7:03
     */
    function showSelectHtml($name){
        $html = "<select name=\"{$name}\" id=\"{$name}\">". $this->showTopClass(0) ."</select>";
        return $html;
    }

    //读取顶级分类
    function showTopClass(int $parentId, string $field='name')
    {
        $msg = '';
        $format = "&nbsp;&nbsp;&nbsp;|---";

        $data = isset($this->data[$parentId]) ? $this->data[$parentId] : [];
        if (empty($data)) {
            return '';
        }
        //处理第一级分类
        foreach ($data as $v) {
            $msg .= "<option value=\"" . $v['id'] . "\">" . $v[$field] . "</option>\n";
            //有下一级，处理下一级
            if (isset($this->data[$v['id']])) {
                $msg .= $this->showChildClass($v['id'], $format, $field);
            }
        }

        return $msg;
    }

    //递归读取无限级子分类
    function showChildClass($id, $format, $field='name')
    {
        $msg = "";
        //该分类的子类
        $data = isset($this->data[$id]) ? $this->data[$id] : [];
        $num = count($data);
        if ($num > 0) {
            $i = 1;
            $iformat = $format . "";

            foreach ($data as $row) {
                if ($i == $num) {
                    $format = str_replace("├", "└", $iformat);
                } else {
                    $format = $iformat;
                }
                $msg = $msg . "<option value=\"{$row['id']}\">{$format}{$row[$field]}</option>\n";
                $i++;
                //还有下一级，继续递归
                if (isset($this->data[$row['id']])) {
                    $format = $format . "&nbsp;&nbsp;&nbsp;|--- ";
                    //$format = str_replace("├", "&nbsp│", $format);
                    $msg .= $this->showChildClass($row['id'], $format, $field);
                }
            }
        }
        return $msg;
    }


    //读取顶级分类
    function getTopClassHtml(int $parentId, string $field='name')
    {
        $list = [];
        $format = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        $data = isset($this->data[$parentId]) ? $this->data[$parentId] : [];
        if (empty($data)) {
            return '';
        }
        //处理第一级分类
        foreach ($data as $v) {
            //print_r($v);exit;
            $v['html'] = $v[$field];
            $v['level'] = 1;
            $v['path'] = '0';
            $v['child'] = isset($this->data[$v['id']]) ? count($this->data[$v['id']]) : 0;
            $list[] = $v;
            //有下一级，处理下一级
            if ($v['child'] > 0) {
                $list = array_merge($list, $this->showChildClassHtml($v['id'], $format, 2, $v['path'] .'-'.$v['id'], $field));
            }
        }
        //print_r($list);
        return $list;
    }

    //递归读取无限级子分类
    function showChildClassHtml($id, $format, $level, $path, $field='name')
    {
        $list = [];
        //该分类的子类
        $data = isset($this->data[$id]) ? $this->data[$id] : [];
        $num = count($data);
        if ($num > 0) {
            $i = 1;
            $iformat = $format . "";
            $ilevel = $level + 1;
            foreach ($data as $row) {
                $row['html'] = $format . $row[$field];
                $row['child'] = isset($this->data[$row['id']]) ? count($this->data[$row['id']]) : 0;
                $row['level'] = $level;
                $row['path'] = $path;
                $list [] = $row;
                //还有下一级，继续递归
                if ($row['child'] > 0) {
                    $mformat = $format . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    $mPath = $path .'-'. $row['id'];
                    //$format = str_replace("├", "&nbsp│", $format);
                    $list = array_merge($list, $this->showChildClassHtml($row['id'], $mformat, $ilevel, $mPath, $field));
                }
            }
        }
        return $list;
    }



    //读取顶级分类
    function getTopClassId(int $parentId)
    {
        $list = [];
        $data = isset($this->data[$parentId]) ? $this->data[$parentId] : [];
        if (empty($data)) {
            return [];
        }
        //处理第一级分类
        foreach ($data as $v) {
            $v['child'] = isset($this->data[$v['id']]) ? count($this->data[$v['id']]) : 0;
            $list[] = $v['id'];
            //有下一级，处理下一级
            if ($v['child'] > 0) {
                $list = array_merge($list, $this->getChildClassId($v['id']));
            }
        }
        //print_r($list);
        return $list;
    }

    //递归读取无限级子分类的ID
    function getChildClassId($id)
    {
        $list = [];
        //该分类的子类
        $data = isset($this->data[$id]) ? $this->data[$id] : [];
        $num = count($data);
        if ($num > 0) {
            $i = 1;
            foreach ($data as $row) {
                $row['child'] = isset($this->data[$row['id']]) ? count($this->data[$row['id']]) : 0;
                $list [] = $row['id'];
                //还有下一级，继续递归
                if ($row['child'] > 0) {
                    $list = array_merge($list, $this->getChildClassId($row['id']));
                }
            }
        }
        return $list;
    }


}


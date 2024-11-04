<?php

namespace Core;

class Validator
{
    use Singleton;

    private $messages = [];
    private $data = [];
    private $errors = [];

    /**
     * 根据 数据 验证规则 验证消息， 创建验证器
     *
     * @param  array  $data  数据
     * @param  array  $rules  验证规则
     * @param  array  $message  验证消息
     * @return $this
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午3:24
     */
    public function make(array $data, array $rules = [], array $messages = [])
    {
        if (empty($rules)) {
            return true;
        }
        $this->data = $data;
        $this->messages = $messages;    //保存为全局变量
        //校验规则
        foreach ($rules as $fieldName => $rule) {
            //验证单个规则，如果一个规则错了，跳出
            if (!$this->oneField($fieldName, $rule)) {
                break;
            }
        }
        return $this;
    }

    /**
     * 验证一个字段，如果字段不存在，直接报错
     *
     * @param $fieldName
     * @param $rule
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午3:29
     */
    public function oneField(string $fieldName, string $rule)
    {
        if (strpos($rule, '|')) {
            $ruleList = explode('|', $rule);
        } else {
            $ruleList = [$rule];

        }
        //一个字段，可能有多个规则
        foreach ($ruleList as $v) {
            if (! $this->oneRule($fieldName, $v)) {
                break;
            }
        }
        return true;
    }

    /**
     * 验证一个字段的一条规则
     *
     * @param  string  $fieldName
     * @param  string  $rule
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午3:44
     */
    public function oneRule(string $fieldName, string $rule)
    {
        $ruleExtension = "";
        //解析规则详情
        if (strpos($rule, ':')) {
            $tmp = explode(':', $rule);
            $ruleName = $tmp[0];
            $ruleExtension = $tmp[1];
        } else {
            $ruleName = $rule;
        }
        $fieldValue = isset($this->data[$fieldName]) ? $this->data[$fieldName] : '';
        //验证每个规则
        switch ($ruleName) {
            case 'required':
                return $this->check_required($fieldName, $ruleName);
            case 'int':
                return $this->check_int($fieldName, $fieldValue, $ruleName);
            case 'size':
                return $this->check_size($fieldName, $fieldValue, $ruleName, $ruleExtension);
            case 'between':
                return $this->check_between($fieldName, $fieldValue, $ruleName, $ruleExtension);
            case 'in':
                return $this->check_in($fieldName, $fieldValue, $ruleName, $ruleExtension);
            case 'same':
                return $this->check_same($fieldName, $fieldValue, $ruleName, $ruleExtension);
        }
        return true;
    }

    /**
     * 返回错误消息
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午3:49
     */
    public function fails()
    {
        if (! empty($this->errors)) {
            $firstError = $this->errors[0];
            return isset($this->messages[$firstError]) ? $this->messages[$firstError] : $firstError;
        }
        return '';
    }

    /**
     * 是否必填
     *
     * @param string $fieldName
     * @param string $ruleName
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午4:40
     */
    private function check_required(string $fieldName, string $ruleName)
    {
        if (!isset($this->data[$fieldName])) {
            $this->errors[] = $fieldName . '.' . $ruleName;
            return false;
        }
        if (empty($this->data[$fieldName])) {
            $this->errors[] = $fieldName . '.' . $ruleName;
            return false;
        }
        return true;
    }

    /**
     * 整型验证
     *
     * @param  string  $fieldName
     * @param   $fieldValue
     * @param  string  $ruleName
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午4:50
     */
    private function check_int(string $fieldName, $fieldValue, string $ruleName){
        if(is_int($fieldValue)){
           return true;
        }else{
            $this->errors[] = $fieldName . '.' . $ruleName;
            return false;
        }
    }

    /**
     * 范围验证， 数字比大小，字符串，比较长度
     *
     * @param  string  $fieldName
     * @param   $fieldValue
     * @param  string  $ruleName
     * @param  string  $ruleExtension
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午4:53
     */
    private function check_between(string $fieldName,  $fieldValue, string $ruleName, string $ruleExtension)
    {
        $ary = explode(',', $ruleExtension);
        if (gettype($fieldValue) == 'string') {
            $len = strlen($fieldValue);
            if ($len < $ary[0] || $len > $ary[1]) {
                $this->errors[] = $fieldName . '.' . $ruleName;
                return false;
            }
        }else{
            if ($fieldValue < $ary[0] || $fieldValue > $ary[1]) {
                $this->errors[] = $fieldName . '.' . $ruleName;
                return false;
            }
        }
        return true;
    }

    /**
     * in区间验证
     *
     * @param  string  $fieldName
     * @param  string  $fieldValue
     * @param  string  $ruleName
     * @param  string  $ruleExtension
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午4:53
     */
    private function check_in(string $fieldName, string $fieldValue, string $ruleName,string $ruleExtension){
        $ary = explode(',', $ruleExtension);
        if(in_array($fieldValue, $ary)) {
            return true;
        }else{
            $this->errors[] = $fieldName . '.' . $ruleName;
            return false;
        }
    }

    /**
     * same 相同验证
     *
     * @param  string  $fieldName
     * @param  string  $fieldValue
     * @param  string  $ruleName
     * @param  string  $ruleExtension
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午4:53
     */
    private function check_same(string $fieldName, string $fieldValue, string $ruleName,string $ruleExtension){
        $otherField = $ruleExtension;
        $otherValue = isset($this->data[$otherField]) ? $this->data[$otherField] : '';
        if($fieldValue == $otherValue) {
            return true;
        }else{
            $this->errors[] = $fieldName . '.' . $ruleName;
            return false;
        }
    }

    /**
     * size 长度验证
     *
     * @param  string  $fieldName
     * @param  string  $fieldValue
     * @param  string  $ruleName
     * @param  string  $ruleExtension
     * @return bool
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/24 下午4:53
     */
    private function check_size(string $fieldName, string $fieldValue, string $ruleName,string $ruleExtension){
        //长度一致
        if(strlen($fieldValue) == $ruleExtension) {
            return true;
        }else{
            $this->errors[] = $fieldName . '.' . $ruleName;
            return false;
        }
    }
}
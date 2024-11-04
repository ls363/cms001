<?php
return [
    'allowFiles' => '.gif,.jpg,.jpeg,.png,.txt,.zip',
    'maxSize' => 5 * 1024 * 1024,
    'folder' => 'Y-m-d', //date中的日期格式，默认按天创建文件夹
    'file' => 'd_###', //支持变量 YmdHis日期格式， ###为随机数
];
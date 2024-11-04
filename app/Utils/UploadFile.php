<?php

namespace App\Utils;

class UploadFile
{
    private $stateInfo = '';
    private $originFileName = '';   //原始文件名
    private $fileExt = '';    //当前文件扩展名
    private $fileName = '';     //新文件名
    private $fileFolder = '';   //上传的文件名
    private $fileSize = 0;      //文件大小
    private $uploadField = '';  //$_FILES，上传控件的名称
    protected $mimeType = '';
    protected $tmpName = '';
    protected $width = 0;
    protected $height = 0;

    protected static $mimeTypeList =
        [
            'video/3gpp' =>
                [
                    0 => '.3gp',
                ],
            'application/vnd.android.package-archive' =>
                [
                    0 => '.apk',
                ],
            'video/x-ms-asf' =>
                [
                    0 => '.asf',
                ],
            'video/x-msvideo' =>
                [
                    0 => '.avi',
                ],
            'application/octet-stream' =>
                [
                    0 => '.bin',
                    1 => '.class',
                    2 => '.exe',
                ],
            'image/bmp' =>
                [
                    0 => '.bmp',
                ],
            'text/plain' =>
                [
                    0 => '.c',
                    1 => '.conf',
                    2 => '.cpp',
                    3 => '.h',
                    4 => '.java',
                    5 => '.log',
                    6 => '.prop',
                    7 => '.rc',
                    8 => '.sh',
                    9 => '.txt',
                    10 => '.xml',
                ],
            'application/msword' =>
                [
                    0 => '.doc',
                ],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' =>
                [
                    0 => '.docx',
                ],
            'application/vnd.ms-excel' =>
                [
                    0 => '.xls',
                ],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' =>
                [
                    0 => '.xlsx',
                ],
            'image/gif' =>
                [
                    0 => '.gif',
                ],
            'application/x-gtar' =>
                [
                    0 => '.gtar',
                ],
            'application/x-gzip' =>
                [
                    0 => '.gz',
                ],
            'text/html' =>
                [
                    0 => '.htm',
                    1 => '.html',
                ],
            'application/java-archive' =>
                [
                    0 => '.jar',
                ],
            'image/jpeg' =>
                [
                    0 => '.jpeg',
                    1 => '.jpg',
                ],
            'application/x-javascript' =>
                [
                    0 => '.js',
                ],
            'audio/x-mpegurl' =>
                [
                    0 => '.m3u',
                ],
            'audio/mp4a-latm' =>
                [
                    0 => '.m4a',
                    1 => '.m4b',
                    2 => '.m4p',
                ],
            'video/vnd.mpegurl' =>
                [
                    0 => '.m4u',
                ],
            'video/x-m4v' =>
                [
                    0 => '.m4v',
                ],
            'video/quicktime' =>
                [
                    0 => '.mov',
                ],
            'audio/x-mpeg' =>
                [
                    0 => '.mp2',
                    1 => '.mp3',
                ],
            'video/mp4' =>
                [
                    0 => '.mp4',
                    1 => '.mpg4',
                ],
            'application/vnd.mpohun.certificate' =>
                [
                    0 => '.mpc',
                ],
            'video/mpeg' =>
                [
                    0 => '.mpe',
                    1 => '.mpeg',
                    2 => '.mpg',
                ],
            'audio/mpeg' =>
                [
                    0 => '.mpga',
                ],
            'application/vnd.ms-outlook' =>
                [
                    0 => '.msg',
                ],
            'audio/ogg' =>
                [
                    0 => '.ogg',
                ],
            'application/pdf' =>
                [
                    0 => '.pdf',
                ],
            'image/png' =>
                [
                    0 => '.png',
                ],
            'application/vnd.ms-powerpoint' =>
                [
                    0 => '.pps',
                    1 => '.ppt',
                ],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' =>
                [
                    0 => '.pptx',
                ],
            'audio/x-pn-realaudio' =>
                [
                    0 => '.rmvb',
                ],
            'application/rtf' =>
                [
                    0 => '.rtf',
                ],
            'application/x-tar' =>
                [
                    0 => '.tar',
                ],
            'application/x-compressed' =>
                [
                    0 => '.tgz',
                ],
            'audio/x-wav' =>
                [
                    0 => '.wav',
                ],
            'audio/x-ms-wma' =>
                [
                    0 => '.wma',
                ],
            'audio/x-ms-wmv' =>
                [
                    0 => '.wmv',
                ],
            'application/vnd.ms-works' =>
                [
                    0 => '.wps',
                ],
            'application/x-compress' =>
                [
                    0 => '.z',
                ],
            'application/x-zip-compressed' =>
                [
                    0 => '.zip',
                ],
        ];

    protected static $extensionMimeList =
        [
            '.3gp' => 'video/3gpp',
            '.apk' => 'application/vnd.android.package-archive',
            '.asf' => 'video/x-ms-asf',
            '.avi' => 'video/x-msvideo',
            '.bin' => 'application/octet-stream',
            '.bmp' => 'image/bmp',
            '.c' => 'text/plain',
            '.class' => 'application/octet-stream',
            '.conf' => 'text/plain',
            '.cpp' => 'text/plain',
            '.doc' => 'application/msword',
            '.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            '.xls' => 'application/vnd.ms-excel',
            '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            '.exe' => 'application/octet-stream',
            '.gif' => 'image/gif',
            '.gtar' => 'application/x-gtar',
            '.gz' => 'application/x-gzip',
            '.h' => 'text/plain',
            '.htm' => 'text/html',
            '.html' => 'text/html',
            '.jar' => 'application/java-archive',
            '.java' => 'text/plain',
            '.jpeg' => 'image/jpeg',
            '.jpg' => 'image/jpeg',
            '.js' => 'application/x-javascript',
            '.log' => 'text/plain',
            '.m3u' => 'audio/x-mpegurl',
            '.m4a' => 'audio/mp4a-latm',
            '.m4b' => 'audio/mp4a-latm',
            '.m4p' => 'audio/mp4a-latm',
            '.m4u' => 'video/vnd.mpegurl',
            '.m4v' => 'video/x-m4v',
            '.mov' => 'video/quicktime',
            '.mp2' => 'audio/x-mpeg',
            '.mp3' => 'audio/x-mpeg',
            '.mp4' => 'video/mp4',
            '.mpc' => 'application/vnd.mpohun.certificate',
            '.mpe' => 'video/mpeg',
            '.mpeg' => 'video/mpeg',
            '.mpg' => 'video/mpeg',
            '.mpg4' => 'video/mp4',
            '.mpga' => 'audio/mpeg',
            '.msg' => 'application/vnd.ms-outlook',
            '.ogg' => 'audio/ogg',
            '.pdf' => 'application/pdf',
            '.png' => 'image/png',
            '.pps' => 'application/vnd.ms-powerpoint',
            '.ppt' => 'application/vnd.ms-powerpoint',
            '.pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            '.prop' => 'text/plain',
            '.rc' => 'text/plain',
            '.rmvb' => 'audio/x-pn-realaudio',
            '.rtf' => 'application/rtf',
            '.sh' => 'text/plain',
            '.tar' => 'application/x-tar',
            '.tgz' => 'application/x-compressed',
            '.txt' => 'text/plain',
            '.wav' => 'audio/x-wav',
            '.wma' => 'audio/x-ms-wma',
            '.wmv' => 'audio/x-ms-wmv',
            '.wps' => 'application/vnd.ms-works',
            '.xml' => 'text/plain',
            '.z' => 'application/x-compress',
            '.zip' => 'application/x-zip-compressed',
        ];

    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS" => '', //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "ERROR_TMP_FILE"           => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED"        => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED"   => "文件类型不允许",
        "ERROR_MIME_TYPE"   => "文件类型被更改，请恢复",
        "ERROR_CREATE_DIR"         => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE"  => "目录没有写权限",
        "ERROR_FILE_MOVE"          => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND"     => "找不到上传文件",
        "ERROR_WRITE_CONTENT"      => "写入文件内容错误",
        "ERROR_UNKNOWN"            => "未知错误",
        "ERROR_DEAD_LINK"          => "链接不可用",
        "ERROR_HTTP_LINK"          => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE"   => "链接contentType不正确",
        "INVALID_URL"              => "非法 URL",
        "INVALID_IP"               => "非法 IP"
    );

    // 读取文件获取MIME_TYPE
    function getFileMIME($filename)
    {
        $file = fopen($filename, "rb");
        $bytes4 = fread($file, 4);
        fclose($file);
        $strInfo = @unpack("C4chars", $bytes4);
        $typeCode = dechex($strInfo ['chars1']) .
            dechex($strInfo ['chars2']) .
            dechex($strInfo ['chars3']) .
            dechex($strInfo ['chars4']); //把十进制转换为十六进制。

        switch ($typeCode) //硬编码值查表
        {
            case "ffd8ffe0" :
            case "ffd8ffe1" :
            case "ffd8ffe2" :
                $type = 'image/jpeg';
                break;
            case "89504e47" :
                $type = 'image/png';
                break;
            case "47494638" :
                $type = 'image/gif';
                break;
            case "504B0304" :
                $type = 'application/zip';
                break;
            case "25504446" :
                $type = 'application/pdf';
                break;
            case "5A5753" :
                $type = 'application/swf';
                break;
            case "3c3f786d" :
                $type = 'application/xml';
                break;
            case "3c68746d" :
                $type = 'application/html';
                break;
            case "0000" :
                $type = 'text/plain';
                break;
            case "2166756e" :
                $type = 'application/x-javascript';
                break;

            default :
                $type = 'application/octet-stream';
                break;
        }
        return $type;
    }

    public function __construct(string $uploadField)
    {
        $this->uploadField = $uploadField;
    }

    public function checkFileName()
    {
        //$_FILES;
    }

    /**
     * 上传错误检查
     * @param  string  $errCode
     * @return string
     */
    private function getStateInfo(string $errCode)
    {
        return ! $this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }


    /**
     * 上传的是$_FILES
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午9:39
     */
    public function upFile()
    {
        //判断上传控件是否存在
        if (! isset($_FILES[$this->uploadField])) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return;
        }

        $file = $_FILES[$this->uploadField];
        //print_r($file);exit;
        $tmp_name = $file['tmp_name'];
        //$mime = $this->getFileMIME($tmp_name);
        //print_r($mime);exit;

        //临时文件不存在
        if (! file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");
            return;
        } else {
            //不是文件类型
            if (! is_uploaded_file($file['tmp_name'])) {
                $this->stateInfo = $this->getStateInfo("ERROR_TMPFILE");
                return;
            }
        }

        $this->mimeType = $file['type'];
        $this->originFileName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileExt = $this->getFileExt();
        $this->fileName = $this->getFileName(8);
        $this->fileFolder = $this->getFolder();
        $this->tmpName = $file['tmp_name'];

        //检查文件大小是否超出限制
        if (! $this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        //print_r("here999");exit;

        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }

        //检测文件的实际类型，判断是否伪造类型
        if(! $this->checkMime()){
            $this->stateInfo = $this->getStateInfo("ERROR_MIME_TYPE");
            return;
        }
        if(strpos($this->mimeType, 'image/') !== false){
            $tmp = getimagesize($file["tmp_name"]);
            $this->width = $tmp[0];
            $this->height = $tmp[1];
        }

        //创建目录失败
        $folder =  $this->getBaseFolder() .'/'. $this->fileFolder;
        $folder = str_replace('//', '/', $folder);
        if (! is_dir($folder) && ! mkdir($folder, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        } else {
            //判断目录是否可写
            if (! is_writeable($folder)) {
                $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
                return;
            }
        }
        $filePath = $folder .'/'. $this->fileName . $this->fileExt;

        //移动文件
        if (!(move_uploaded_file($file["tmp_name"], $filePath) && file_exists($filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_MOVE");
            return;
        } else { //移动成功
            $this->stateInfo = "";
        }
        return $this->getFileInfo();
    }

    /**
     * 返回错误信息
     *
     * @return mixed
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午11:41
     */
    public function getErrorInfo(){
        return $this->stateInfo;
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            "state" => $this->stateInfo,
            "url" => $this->fileFolder .'/'. $this->fileName . $this->fileExt,   //完整的文件路径
            "title" => $this->fileName,     //新的文件名
            "original" => $this->originFileName, //原始文件名
            "type" => $this->fileExt,  //文件扩展名
            "size" => $this->fileSize,   //文件大小
            "folder" => $this->fileFolder, //文件夹
            "mime_type" => $this->mimeType,
            "width" => $this->width,
            "height" => $this->height
        );
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->fileExt, explode(',', config("file.allowFiles")));
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function checkSize()
    {
        return $this->fileSize <= config("file.maxSize");
    }

    /**
     * 检测文件的mime类型
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/9/25 上午11:39
     */
    private function checkMime(){
        $mimeExt = self::$extensionMimeList[$this->fileExt] ?? '';
        //通过file获取到的类型，通常是 application/octet-stream
        if($mimeExt != $this->mimeType){
            //获取file的实际类型
            $mimeRb = $this->getFileMIME($this->tmpName);
            if($mimeExt == $mimeRb){
                $this->mimeType = $mimeRb;
                return true;
            }
            return false;
        }else{
            return true;
        }
    }

    /**
     * 获取上传的根目录
     *
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午11:07
     */
    public function getBaseFolder(){
        return UPLOAD_PATH;
    }

    /**
     * 获取上传路径
     *
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午9:07
     */
    public function getFolder()
    {
        return  date(config('file.folder'));
    }

    /**
     * 获取随机文件名
     *
     * @return string
     * @author lichunguang 153102250@qq.com
     * @since 2022/4/30 下午9:06
     */
    public function getFileName(int $num)
    {
        $str = config('file.file'); //文件模板
        $str = date($str);
        if (strpos($str, '###') !== false) {
            $randomString = getRandomString($num);
            $str = str_replace('###', $randomString, $str);
        }
        return $str;
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->originFileName, '.'));
    }

}
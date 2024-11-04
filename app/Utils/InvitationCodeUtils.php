<?php
namespace App\Utils;

class InvitationCodeUtils
{

    // 定义字符集
    private static array $CHARS = ["Y", "2", "U", "K", "X", "V", "C", "F", "N", "S", "6", "8", "G", "Z", "Q", "7", "A", "9", "P", "H", "5", "M", "R", "L", "D", "J", "4", "T", "W", "E", "3", "B"];

    private const CHARS_LENGTH = 32;
    private const SLAT = 3312427;
    private const PRIME1 = 3;
    private const PRIME2 = 11;

    // 生成邀请码
    public static function gen($id, $length = 6): string {
        // 对 ID 进行加密处理
        $id = $id * self::PRIME1 + self::SLAT;
        $b = [];
        $b[0] = $id;
        for ($i = 0; $i < $length - 1; $i++) {
            $b[$i + 1] = $b[$i] / self::CHARS_LENGTH;
            $b[$i] = (int)($b[$i] + $i * $b[0]) % self::CHARS_LENGTH;
        }

        //print_r($b);//exit;

        // 计算邀请码索引
        $tmp = 0;
        for ($i = 0; $i < $length - 2; $i++) {
            $tmp += $b[$i];
        }
        $b[$length - 1] = $tmp * self::PRIME1 % self::CHARS_LENGTH;

        //print_r($b);exit;

        // 混淆生成邀请码
        $codeIndexArray = [];
        for ($i = 0; $i < $length; $i++) {
            $codeIndexArray[$i] = $b[$i * self::PRIME2 % $length];
        }

        $buffer = '';
        foreach ($codeIndexArray as $index) {
            $buffer .= self::$CHARS[$index];
        }
        return $buffer;
    }

    // 解密邀请码获取原始 ID
    public static function decode($code): ?int {

        $length = strlen($code);

        // 将字符转换为对应数字
        $a = [];
        for ($i = 0; $i < $length; $i++) {
            $c = $code[$i];
            $index = self::findIndex($c);
            if ($index == -1) {
                return null;
            }
            $a[$i * self::PRIME2 % $length] = $index;
        }

        // 逆向计算出原始 ID
        $b = [];
        for ($i = $length - 2; $i >= 0; $i--) {
            $b[$i] = ($a[$i] - $a[0] * $i + self::CHARS_LENGTH * $i) % self::CHARS_LENGTH;
        }

        $res = 0;
        for ($i = $length - 2; $i >= 0; $i--) {
            $res += $b[$i];
            $res *= ($i > 0 ? self::CHARS_LENGTH : 1);
        }
        return (int)(($res - self::SLAT) / self::PRIME1);
    }

    // 查找字符在字符集中的位置
    public static function findIndex($c): int {
        foreach (self::$CHARS as $key => $char) {
            if ($char == $c) {
                return $key;
            }
        }
        return -1;
    }
}
/**
// 测试示例
//$id = 123456;
$idList = [1,12,123,1234,12345,123456,1234567,12345678, 123456789,999999999];
foreach ($idList as $id) {
$encryptedCode = InvitationCodeUtil::gen($id, 9);
echo "加密后的邀请码: $encryptedCode\n";  // 8N79U4

$decodedUID = InvitationCodeUtil::decode($encryptedCode);
echo "解密后的 UID: $decodedUID\n";  // 123456
}
 *
 */
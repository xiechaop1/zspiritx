<?php
class Curl
{
    /**

     * 通过CURL发送HTTP请求

     * @param string $url  //请求URL

     * @param array $postFields //请求参数

     * @return mixed

     */

    public static function curlPost($url,$postFields, $header = array()){

        $postFields = http_build_query($postFields);

        $ch = curl_init ();

        curl_setopt ( $ch, CURLOPT_POST, 1 );

        curl_setopt ( $ch, CURLOPT_HEADER, 0 );

        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        curl_setopt ( $ch, CURLOPT_URL, $url );

        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );

        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );

        $result = curl_exec ( $ch );

        curl_close ( $ch );

        return $result;

    }

    public static function curlGet($url, $header = []){

        $ch = curl_init ();

        if (!empty($header)) {
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt ( $ch, CURLOPT_HEADER, 0);

        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        curl_setopt ( $ch, CURLOPT_URL, $url );

        $result = curl_exec ( $ch );

        curl_close ( $ch );

        return $result;

    }
}

$maxPage = 500;
$pIds = [
    '5924122',
    '61199387471',
    '10400852110',
    '1450426491',
    '10392976539'
];
foreach ($pIds as $pid) {
    for ($page = 1; $page <= $maxPage; $page++) {
        echo $pid . ' ' . $page . '/' . $maxPage . "\n";
//    $uri = 'https://api.m.jd.com/?appid=item-v3&functionId=pc_club_productPageComments&client=pc&clientVersion=1.0.0&t=1694673410511&loginType=3&uuid=122270672.16733205344791835249701.1673320534.1694662045.1694672885.223&productId=5924122&score=0&sortType=5&page=' . $page . '&pageSize=10&isShadowSku=0&rid=0&fold=1&bbtf=&shield=';
        $uri = 'https://api.m.jd.com/?appid=item-v3&functionId=pc_club_productPageComments&client=pc&clientVersion=1.0.0&t=1694677923182&loginType=3&uuid=122270672.16733205344791835249701.1673320534.1694672885.1694677904.224&productId=' . $pid . '&score=0&sortType=5&page=' . $page . '&pageSize=10&isShadowSku=0&rid=0&fold=1&bbtf=&shield=';
//        $uri = 'https://api.m.jd.com/?appid=item-v3&functionId=pc_club_getProductPageFoldComments&client=pc&clientVersion=1.0.0&t=1694678280579&loginType=3&uuid=122270672.16733205344791835249701.1673320534.1694672885.1694677904.224&productId=' . $pid . '&score=0&sortType=5&page=' . ($page - 1 ) . '&pageSize=10';

//$data = file_get_contents($uri);

        $data = Curl::curlGet($uri);

//    $data = file_get_contents('./tmp.log');

        $data = htmlspecialchars_decode($data);
        $data = iconv('gbk', 'utf8', $data);

        $data = json_decode($data, true);
        $errinfo = json_last_error();
        if ($errinfo) {
            echo 'json_decode error: ' . $errinfo;
            exit;
        }

        $dc = $data['comments'];

        echo 'DataCount: ' . count($dc) . "\n";

        if (empty($dc)) {
            break;
        }

        $cont = [];
        foreach ($dc as $row) {
            if ($pid != '1450426491') {
                $cont[] = $row['guid'] . "\t" . $row['productColor'];
            } else {
                $cont[] = $row['guid'] . "\t" . $row['productSize'];
            }
        }
        file_put_contents('./ret_' . $pid . '_o.csv', implode("\n", $cont), FILE_APPEND);
    }
}

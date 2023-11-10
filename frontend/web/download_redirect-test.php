<?php

// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('You are not allowed to access this file.');
}

// Todo: 地址强制且是顶级域名，如果有调整，请记得调整此处
header('location: https://www.zspiritx.com.cn/download/redirect');

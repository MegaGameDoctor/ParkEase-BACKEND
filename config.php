<?php

# Подключение к базе данных
$host = "ХОСТ";
$dbuser = "ПОЛЬЗОВАТЕЛЬ";
$dbpass = "ПАРОЛЬ";
$dbname = "НАЗВАНИЕ БАЗЫ ДАННЫХ";

if(!function_exists('gen_token') && !function_exists('getIP')) {
function gen_token()
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, 10);
}

function getIP()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    return $ip;
}
}
?>
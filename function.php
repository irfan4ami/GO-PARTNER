<?php

function curl($url, $post, $headers, $follow = false, $method = null){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($follow == true)
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
    if ($method !== null)
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($headers !== null)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($post !== null)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);
    $header = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $body = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
    $cookies = array();
    foreach ($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }
    return array(
        $header,
        $body,
        $cookies,
        $status_code
    );
}


function curl_pd($url, $post, $headers, $method, $follow = false){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($follow == true)
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
    if ($method !== null)
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($headers !== null)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($post !== null)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);
    $header = substr($result, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $body = substr($result, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
    $cookies = array();
    foreach ($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }
    return array(
        $header,
        $body,
        $cookies,
        $status_code
    );
}



function get_post($url, $data, $header){
    if ($data) {
        $res = curl($url, $data, $header);
    } else {
        $res = curl($url, null, $header);
    }
    return $res;
}

function put_del($url, $data, $header, $method){
    if ($data) {
        $res = curl_pd($url, $data, $header, $method);
    } else {
        $res = curl_pd($url, null, $header, $method);
    }
    return $res;
}



function save($data, $file){
    $handle = fopen($file, 'a+');
    fwrite($handle, $data);
    fclose($handle);
}

function sarep($data, $file){
    $handle = fopen($file, 'w');
    fwrite($handle, $data);
    fclose($handle);
}

function color($color = "default" , $text){
    $arrayColor = array(
        'grey'      => '1;30',
        'red'       => '1;31',
        'green'     => '1;32',
        'yellow'    => '1;33',
        'blue'      => '1;34',
        'purple'    => '1;35',
        'nevy'      => '1;36',
        'white'     => '1;0',
    );  
    return "\033[".$arrayColor[$color]."m".$text."\033[0m";
}


function input(){
    $input = fgets(STDIN);
    $input = trim($input);
    return $input;
}


function uuid($length){
    $str = "";
    $characters = array_merge(range('0', '9'), range('a', 'z'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}


function remove_space($var){
    $new = str_replace("\n", "", $var);
    $new = str_replace("\t", "", $new);
    $new = str_replace(" ", "", $new);
    return $new;
}



function random(){
    $url = 'https://gitburn.my.id/fake-generator/';
    $headers = array();
        $headers[] = 'Host: gitburn.my.id';
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        $headers[] = 'User-agent: okhttp/4.10.0';
    $get = get_post($url, null, $headers);
    $jsget = json_decode($get[1]);
    return array(
        $jsget->fullname,
        $jsget->email
    );
}



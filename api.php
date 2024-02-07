<?php

function data_akun(){
    if (!file_exists('.akun')){
        login();
    }
    $akun = explode(";", file_get_contents('.akun'));
    $token = $akun[0];
    $uuid  = $akun[1];
    return array(
        $token,
        $uuid
    );
}


function headers(){
    $access_token = data_akun()[0];
    $uuid = data_akun()[1];
    $headers = array();
        $headers[] = 'Host: driver.gojekapi.com';
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        $headers[] = 'X-appversion: 4.0.1';
        $headers[] = 'X-deviceos: Android';
        $headers[] = 'Accept-language: id';
        $headers[] = 'X-user-locale: id_ID';
        $headers[] = 'Authorization: Bearer '.$access_token;
        $headers[] = 'X-uniqueid: '.$uuid;
        $headers[] = 'User-Agent: okhttp/4.10.0';
    return $headers;
}

function headers_login($uuid, $cl){
    $headers = array();
        $headers[] = 'host: driver.gojekapi.com';
        $headers[] = 'Content-Type: application/json; charset=UTF-8';
        $headers[] = 'x-appversion: 4.0.1';
        $headers[] = 'x-deviceos: Android';
        $headers[] = 'x-user-locale: id_ID';
        $headers[] = 'accept-language: id';
        $headers[] = 'Content-Length: '.$cl;
        $headers[] = 'User-Agent: okhttp/4.10.0';   
        $headers[] = 'x-uniqueid: '.$uuid;

    return $headers;
}

function lead(){
    $url = "https://driver.gojekapi.com/lead/v1/lead";
    $headers = headers();
    $get = get_post($url, null, $headers);
    return $get;
}


function cek_token(){
    $data = data_akun();
    $cek = lead();
    if ($cek[3] != 200){
        unlink('.akun');
        login();
    }
}


function cek_type_driver(){
    $url = "https://driver.gojekapi.com/lead/v1/partner_types";
    $headers = headers();
    $get = get_post($url, null, $headers);
    $no = 1;
    foreach (json_decode($get[1])->data as $type){
        echo @color('yellow', "\n[$no]");
        echo @color('green', " TYPE  : ");
        echo @color('nevy', $type->title);
        echo @color('green', "\n    DESC  : ");
        echo @color('nevy', $type->description);
        $no++;
    }
    echo @color('nevy', "\nPILIH : ");
    $pilih = input();
    $partner_type = json_decode($get[1])->data[$pilih - 1]->partner_type_id;
    if ($partner_type == ''){
        echo @color('red', "PILIHAN YANG SALAH !!!\n");
        sleep(5);
        main();
    }
    return $partner_type;
}


function set_type($partner_type_id){    
    clear();
    $url = "https://driver.gojekapi.com/lead/v2/lead/set_type";
    $body = '{"partner_type_id":"'.$partner_type_id.'"}';
    $headers = headers();
    $get = put_del($url, $body, $headers, "PUT");
    $jsget = json_decode($get[1]);
    echo @color('green', "STATUS  : ");
    echo @color('nevy', $jsget->data->status);
    echo @color('green', "\nTYPE    : ");
    echo @color('nevy', $jsget->data->driver_type);
}


function list_kota($partner_type_id){
    $url = 'https://driver.gojekapi.com/lead/v1/dropdown_items?key=city_id&lead_id='.$partner_type_id;
    $headers = headers();
    $get = get_post($url, null, $headers);
    return $get;
}


function check_pilihan($partner_type_id){
    unlink('.list');
    echo @color('yellow', "\n[1] "); echo @color('green', "CEK SATU KOTA");
    echo @color('yellow', "\n[2] "); echo @color('green', "CEK SELURUHNYA");
    echo @color('nevy', "\nPILIH : ");
    $pilih = input();
    $cek = list_kota($partner_type_id);
    if ($pilih == "1"){
        $no = 1;
        foreach (json_decode($cek[1])->data as $list){
            echo @color('yellow', "\n[$no] ");
            echo @color('nevy', $list->name);
            $no++;
        }
        echo @color('nevy', "\nPILIH : ");
        $pilih = input();
        $city_id = json_decode($cek[1])->data[$pilih - 1]->id;    
        echo @color('nevy', "\n\n\n");
        cek_ketersediaan($city_id);
    } else if ($pilih == "2"){
        $no = 1;
        foreach (json_decode($cek[1])->data as $list){
            echo @color('yellow', "\n[$no] ");
            cek_ketersediaan($list->id);
            $no++;
        }
    }
}



function cek_ketersediaan($city_id){
    $random = random();
    $url = "https://driver.gojekapi.com/lead/v1/update";
    $body = '{"city_id":"'.$city_id.'","email":"'.$random[1].'","name":"'.$random[0].'","driver_tipe": "1"}';
    $headers = headers();
    $get = put_del($url, $body, $headers, "PUT");
    $jsget = json_decode($get[1]);
    if ($jsget->data == null){
        echo @color('red', $jsget->errors[0]->message_title);
    } else {
        echo @color('nevy', $jsget->data->city->name." DIBUKA");
    }
    return $get;
}



function req_otp($uuid, $number){ 
    $url = "https://driver.gojekapi.com/lead/v3/login";
    $body = '{"country_code":"ID","phone":"+62-'.$number.'"}';
    $cl = strlen($body);
    $headers = headers_login($uuid, $cl);
    $get = get_post($url, $body, $headers);
    return $get;
}



function req_otp2($uuid){ 
    $url = "https://driver.gojekapi.com/lead/v3/login/status/legacy";
    $headers = headers_login($uuid, "");
    $get = get_post($url, null, $headers);
    return $get;
}



function verif_otp($uuid, $number, $otp){    
    $url = "https://driver.gojekapi.com/lead/v1/verify";
    $body = '{"otp":"'.$otp.'","phone":"+62-'.$number.'","request_id":"legacy"}';
    $cl = strlen($body);
    $headers = headers_login($uuid, $cl);
    $get = get_post($url, $body, $headers);
    return $get;
}


function login(){
    input:
    $uuid = uuid(16);
    echo @color('green', "NOMOR : +62");
    $number = input();
    $req = req_otp($uuid, $number);
    if ($req[3] != 200){
        echo @color('red', "$req[1]\n");
        goto input;
    }
    $req2 = req_otp2($uuid);
    echo "$req2[1]\n\n";
    echo @color('green', "OTP   : ");
    $otp = input();
    $verif = verif_otp($uuid, $number, $otp);
    if ($verif[3] != 200){
        echo @color('red', "$verif[1]\n");
        goto input;
    }
    $access_token = json_decode($verif[1])->data->token;
    sarep("$access_token;$uuid", '.akun');
}





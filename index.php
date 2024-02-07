<?php
error_reporting(0);
require('function.php');
require('api.php');


main();








function banner(){
    echo @color('red', "


     ██████╗  ██████╗       ██████╗ 
    ██╔════╝ ██╔═══██╗      ██╔══██╗
    ██║  ███╗██║   ██║█████╗██████╔╝");echo @color('white', "
    ██║   ██║██║   ██║╚════╝██╔═══╝ 
    ╚██████╔╝╚██████╔╝      ██║     
     ╚═════╝  ╚═════╝       ╚═╝
     
     \n");
}


function clear(){
    $clear = exec('clear');
    echo $clear;
    banner();
}


function main(){
    clear();
    cek_token();
    $partner_type_id = cek_type_driver();
    set_type($partner_type_id);
    check_pilihan($partner_type_id);
}




<?php
    //Effettua redirect su HTTPS 
    if( empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on'  ){
        
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST:URI']);
        exit();
    } 
?>
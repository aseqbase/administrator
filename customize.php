<?php
if("/".\_::$DIRECTION !== MiMFa\Library\User::$InHandlerPath) ACCESS(9, assign:true, die:true);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
\_::$CONFIG->AllowSigning = true;
\_::$CONFIG->Handlers = array(
        "/^post(\/|\?|$)/i"=>"post",
        "/^page(\/|\?|$)/i"=>"page",
        "/^query(\/|\?|$)/i"=>"query",
        "/^search(\/|\?|$)/i"=>"search",
        "/^tag(\/|\?|$)/i"=>"tag",
        "/^sign(\/|\?|$)/i"=>"sign",
        "/^category(\/|\?|$)/i"=>"category"
    );
?>
<?php
if ("/" . \_::$DIRECTION !== MiMFa\Library\User::$InHandlerPath)
    ACCESS(9, assign: true, die: true);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
\_::$CONFIG->AllowSigning = true;
\_::$CONFIG->Router->Route("/^\/post(\/|\?|$)/i")->All("post");
\_::$CONFIG->Router->Route("/^\/page(\/|\?|$)/i")->All("page");
\_::$CONFIG->Router->Route("/^\/query(\/|\?|$)/i")->All("query");
\_::$CONFIG->Router->Route("/^\/search(\/|\?|$)/i")->All("search");
\_::$CONFIG->Router->Route("/^\/tag(\/|\?|$)/i")->All("tag");
\_::$CONFIG->Router->Route("/^\/sign(\/|\?|$)/i")->All("sign");
\_::$CONFIG->Router->Route("/^\/category(\/|\?|$)/i")->All("category");
?>
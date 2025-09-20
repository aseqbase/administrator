<?php
\_::$Back->Router
    ->On()->Reset()
    ->if(!auth(\_::$Config->AdminAccess))
        ->On("$|admin")->Default(fn() => view("part", ["Name" => \User::$InHandlerPath]))
        ->On()->Default(\_::$Config->DefaultRouteName)
    ->else()
        ->On("admin")->Reset()->Default(\_::$Direction, alternative: \_::$Config->DefaultRouteName)
        ->On()->Default(\_::$Config->DefaultRouteName);
?>
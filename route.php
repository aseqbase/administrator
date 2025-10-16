<?php
\_::$Aseq
    ->On()->Reset()
    ->if(!auth(\_::$Config->AdminAccess))
        ->On("$|admin")->Default(fn() => view("part", ["Name" => \User::$InHandlerPath]))
        ->On()->Default(\_::$Config->DefaultRouteName)
    ->else()
        ->On("admin")->Reset()->Default(\_::$Base->Direction, alternative: \_::$Config->DefaultRouteName)
        ->On()->Default(\_::$Config->DefaultRouteName);
?>
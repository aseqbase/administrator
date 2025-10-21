<?php
\_::$Router
    ->On()->Reset()
    ->if(!\_::$User->GetAccess(\_::$User->AdminAccess))
        ->On("$|admin")->Default(fn() => view("part", ["Name" => \_::$User->InHandlerPath]))
        ->On()->Default(\_::$Router->DefaultRouteName)
    ->else()
        ->On("admin")->Reset()->Default(\_::$Address->Direction, alternative: \_::$Router->DefaultRouteName)
        ->On()->Default(\_::$Router->DefaultRouteName);
?>
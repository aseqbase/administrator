<?php
\_::$Back->Router
    ->Route->Reset()
    ->if(!auth(\_::$Config->AdminAccess))
        ->Route("$|admin")->Default(fn() => view("part", ["Name" => MiMFa\Library\User::$InHandlerPath]))
        ->Route->Default(\_::$Config->DefaultRouteName)
    ->else
        ->Route("admin")->Reset()->Default(\Req::$Direction, alternative: \_::$Config->DefaultRouteName)
        ->Route->Default(\_::$Config->DefaultRouteName);
?>
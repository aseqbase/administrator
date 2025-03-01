<?php
\_::$Back->Router
    ->if(!auth(\_::$Config->AdminAccess))
    ->Route()->Reset()
    ->Route("$|admin")->Default(fn() => view("part", ["Name" => MiMFa\Library\User::$InHandlerPath]))
    ->Route()->Default(\_::$Config->DefaultRouteName)
    ->else()
    ->Route()->Reset()
    ->Route("admin")->Reset()->Default(\Req::$Direction, alternative: \_::$Config->DefaultRouteName)
    ->Route()->Default(\_::$Config->DefaultRouteName);
;
?>
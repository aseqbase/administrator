<?php
\_::$Back->Router
    ->if(!auth(\_::$Config->AdminAccess))
    ->Route()->Default(fn() => view("part", ["Name" => MiMFa\Library\User::$InHandlerPath]))
    ->else()
    ->Route()->Reset()
    ->Route("admin")->Reset()->Default(\Req::$Direction, \_::$Config->DefaultRouteName)
    ->Route("$")->Default(fn() => view(\_::$Config->DefaultRouteName, ["Name" => "home"]))
    ->Route()->Default(\_::$Config->DefaultRouteName);
;
?>
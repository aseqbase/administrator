<?php
$data = $data??[];
$routeHandler = function ($data) {
    return \MiMFa\Library\Revise::ToString(\_::$Back);
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->SuperAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "cog",
            "Title" => "Configuration"
        ]);
    })
    ->Default(fn()=>response($routeHandler($data)))
    ->Handle();
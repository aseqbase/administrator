<?php
$data = $data??[];
$routeHandler = function ($data) {
    return \MiMFa\Library\Revise::ToString(\_::$User);
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->SuperAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "user-cog",
            "Title" => "Management"
        ]);
    })
    ->Default(fn()=>response($routeHandler($data)))
    ->Handle();
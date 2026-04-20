<?php
$data = $data??[];
$routeHandler = function ($data) {
    return $data;
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "th",
            "Title" => "Templates"
        ]);
    })
    ->Default(fn()=>response($routeHandler($data)))
    ->Handle();
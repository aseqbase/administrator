<?php
$data = $data??[];
$routeHandler = function () use($data) {
    return $data;
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "th",
            "Title" => "Templates"
        ]);
    })
    ->Default(fn()=>response($routeHandler()))
    ->Handle();
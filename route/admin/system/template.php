<?php
$data = $data??[];
$routeHandler = function () use($data) {
    return \MiMFa\Library\Revise::ToString(\_::$Front->CreateTemplate());
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "eye",
            "Title" => "Appearance"
        ]);
    })
    ->Default(fn()=>response($routeHandler()))
    ->Handle();
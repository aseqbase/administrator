<?php
$data = $data??[];
$routeHandler = function () use($data) {
    \MiMFa\Library\Revise::Render(\_::$Front->CreateTemplate());
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
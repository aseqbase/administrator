<?php
$data = $data ?? [];
$routeHandler = function ($data) {
    return \MiMFa\Library\Revise::ToString(\_::$Front);
};

(new Router())
    ->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "edit",
            "Title" => "Information"
        ]);
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();
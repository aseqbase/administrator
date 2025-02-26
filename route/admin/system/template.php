<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "system/template",
            "Image" => "/asset/symbol/pallete.png",
            "Title" => "Template"
        ]);
    })
    ->Default(function () {
        part("system/template");
    })
    ->Handle();
?>
<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/templates",
            "Image" => "/asset/symbol/dashboard.png",
            "Title" => "Templates"
        ]);
    })
    ->Default(function () {
        part("table/templates");
    })
    ->Handle();
?>
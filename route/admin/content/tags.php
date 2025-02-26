<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/tags",
            "Image" => "/asset/symbol/directory.png",
            "Title" => "Tags Management"
        ]);
    })
    ->Default(function () {
        part("table/tags");
    })
    ->Handle();
?>
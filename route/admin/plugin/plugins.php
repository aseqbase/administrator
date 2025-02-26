<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/plugins",
            "Image" => "/asset/symbol/plugin.png",
            "Title" => "Plugins Management"
        ]);
    })
    ->Default(function () {
        part("table/plugins");
    })
    ->Handle();
?>
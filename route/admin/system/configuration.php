<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->SuperAccess))
    ->Get(function () {
        view("part", [
            "Name" => "system/configuration",
            "Image" => "/asset/symbol/service.png",
            "Title" => "Configuration"
        ]);
    })
    ->Default(function () {
        part("system/configuration");
    })
    ->Handle();
?>
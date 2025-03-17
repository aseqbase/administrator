<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->SuperAccess))
    ->Get(function () {
        view("part", [
            "Name" => "system/configuration",
            "Image" => "puzzle-piece",
            "Title" => "Configuration"
        ]);
    })
    ->Default(function () {
        part("system/configuration");
    })
    ->Handle();
?>
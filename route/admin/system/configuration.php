<?php
(new Router())
->if(auth(\_::$Config->SuperAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/configuration",
            "Image" => "puzzle-piece",
            "Title" => "Configuration"
        ]);
    })
    ->Default(function () {
        part("admin/system/configuration");
    })
    ->Handle();
?>
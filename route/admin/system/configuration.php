<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->SuperAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/configuration",
            "Image" => "cog",
            "Title" => "Configuration"
        ]);
    })
    ->Default(function () {
        part("admin/system/configuration");
    })
    ->Handle();
?>
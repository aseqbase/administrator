<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/template",
            "Image" => "eye",
            "Title" => "Appearance"
        ]);
    })
    ->Default(function () {
        part("admin/system/template");
    })
    ->Handle();
?>
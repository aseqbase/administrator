<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/assets",
            "Image" => "file",
            "Title" => "Assets Management"
        ]);
    })
    ->Default(function () {
        part("admin/system/assets");
    })
    ->Handle();
?>
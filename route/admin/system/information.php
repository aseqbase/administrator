<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/information",
            "Image" => "palette",
            "Title" => "Information"
        ]);
    })
    ->Default(function () {
        part("admin/system/information");
    })
    ->Handle();
?>
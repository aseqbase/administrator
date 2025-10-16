<?php
(new Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/information",
            "Image" => "quote-left",
            "Title" => "Information"
        ]);
    })
    ->Default(function () {
        part("admin/system/information");
    })
    ->Handle();
?>
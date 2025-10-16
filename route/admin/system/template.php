<?php
(new Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/template",
            "Image" => "/asset/symbol/pallete.png",
            "Title" => "Template"
        ]);
    })
    ->Default(function () {
        part("admin/system/template");
    })
    ->Handle();
?>
<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
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
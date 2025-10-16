<?php
(new Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/templates",
            "Image" => "th",
            "Title" => "Templates"
        ]);
    })
    ->Default(function () {
        part("admin/table/templates");
    })
    ->Handle();
?>
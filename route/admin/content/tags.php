<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/tags",
            "Image" => "tags",
            "Title" => "Tags Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/tags");
    })
    ->Handle();
?>
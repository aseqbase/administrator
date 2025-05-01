<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/categories",
            "Image" => "code-fork",
            "Title" => "Categories Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/categories");
    })
->Handle();
?>
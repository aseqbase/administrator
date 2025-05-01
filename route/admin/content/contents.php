<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/contents",
            "Image" => "th-large",
            "Title" => "Contents Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/contents");
    })
    ->Handle();
?>
<?php
(new Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/contents",
            "Image" => "file",
            "Title" => "Contents Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/contents");
    })
    ->Handle();
?>
<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/comments",
            "Image" => "comment",
            "Title" => "Comments Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/comments");
    })
    ->Handle();
?>
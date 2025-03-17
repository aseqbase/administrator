<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/comments",
            "Image" => "comment",
            "Title" => "Comments Management"
        ]);
    })
    ->anyway()->Default(function () {
        part("table/comments");
    })
    ->Handle();
?>
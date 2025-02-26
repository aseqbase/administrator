<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/comments",
            "Image" => "/asset/symbol/chat.png",
            "Title" => "Comments Management"
        ]);
    })
    ->Default(function () {
        part("table/comments");
    })
    ->Handle();
?>
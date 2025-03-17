<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/contents",
            "Image" => "th-large",
            "Title" => "Contents Management"
        ]);
    })
    ->Default(function () {
        part("table/contents");
    })
    ->Handle();
?>
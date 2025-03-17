<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "system/information",
            "Image" => "quote-left",
            "Title" => "Information"
        ]);
    })
    ->Default(function () {
        part("system/information");
    })
    ->Handle();
?>
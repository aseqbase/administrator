<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/plugins",
            "Image" => "puzzle-piece",
            "Title" => "Plugins Management"
        ]);
    })
    ->Default(function () {
        part("table/plugins");
    })
    ->Handle();
?>
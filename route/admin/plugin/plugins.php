<?php
(new Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/plugins",
            "Image" => "puzzle-piece",
            "Title" => "Plugins Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/plugins");
    })
    ->Handle();
?>
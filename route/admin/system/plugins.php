<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
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
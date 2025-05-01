<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/messages",
            "Image" => "envelope",
            "Title" => "Messages Management"
        ]);
    })
    ->anyway()->Default(function () {
        part("admin/table/messages");
    })
    ->Handle();
?>
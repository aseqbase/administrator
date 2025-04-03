<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/messages",
            "Image" => "envelope",
            "Title" => "Messages Management"
        ]);
    })
    ->anyway()->Default(function () {
        part("table/messages");
    })
    ->Handle();
?>
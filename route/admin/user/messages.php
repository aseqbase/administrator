<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/messages",
            "Image" => "envelope",
            "Title" => "Messages Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/messages");
    })
    ->Handle();
?>
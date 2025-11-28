<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/sessions",
            "Image" => "clock",
            "Title" => "'Sessions' Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/sessions");
    })
    ->Handle();
?>
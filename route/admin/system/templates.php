<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/templates",
            "Image" => "th",
            "Title" => "Templates"
        ]);
    })
    ->Default(function () {
        part("admin/table/templates");
    })
    ->Handle();
?>
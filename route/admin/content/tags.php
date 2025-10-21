<?php
(new Router())
->if(\_::$User->GetAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/tags",
            "Image" => "tags",
            "Title" => "Tags Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/tags");
    })
    ->Handle();
?>
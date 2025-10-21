<?php
(new Router())->if(\_::$User->GetAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/contents",
            "Image" => "file",
            "Title" => "Contents Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/contents");
    })
    ->Handle();
?>
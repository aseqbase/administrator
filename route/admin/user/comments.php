<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/comments",
            "Image" => "comment",
            "Title" => "Comments Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/comments");
    })
    ->Handle();
?>
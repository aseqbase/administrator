<?php
(new Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/users",
            "Image" => "user",
            "Title" => "Users Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/users");
    })
    ->Handle();
?>
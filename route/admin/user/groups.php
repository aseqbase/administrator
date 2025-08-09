<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/usergroups",
            "Image" => "user-group",
            "Title" => "User Groups Management"
        ]);
    })
    ->Default(function () {
        part("admin/table/usergroups");
    })
    ->Handle();
?>
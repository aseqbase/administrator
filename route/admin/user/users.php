<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/users",
            "Image" => "/asset/symbol/user.png",
            "Title" => "Users Management"
        ]);
    })
    ->Default(function () {
        part("table/users");
    })
    ->Handle();
?>
<?php
(new MiMFa\Library\Router())->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "table/usergroups",
            "Image" => "/asset/symbol/contact.png",
            "Title" => "User Groups Management"
        ]);
    })
    ->Default(function () {
        part("table/usergroups");
    })
    ->Handle();
?>
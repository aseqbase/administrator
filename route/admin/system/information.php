<?php
(new Router())
->if(\_::$User->GetAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/information",
            "Image" => "quote-left",
            "Title" => "Information"
        ]);
    })
    ->Default(function () {
        part("admin/system/information");
    })
    ->Handle();
?>
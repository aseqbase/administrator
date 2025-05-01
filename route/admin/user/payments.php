<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/payments",
            "Image" => "credit-card",
            "Title" => "Payments Management"
        ]);
    })
    ->anyway()->Default(function () {
        part("admin/table/payments");
    })
    ->Handle();
?>
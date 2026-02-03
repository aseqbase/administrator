<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/storage/static",
            "Image" => "folder",
            "Title" => "'Organized' 'Files' 'Management'"
        ]);
    })
    ->Default(function () {
        part("admin/storage/static");
    })
    ->Handle();
?>
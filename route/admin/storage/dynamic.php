<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/storage/dynamic",
            "Image" => "download",
            "Title" => "'Dynamic' 'Files' 'Management'"
        ]);
    })
    ->Default(function () {
        part("admin/storage/dynamic");
    })
    ->Handle();
?>
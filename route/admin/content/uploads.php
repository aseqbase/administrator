<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/system/uploads",
            "Image" => "upload",
            "Title" => "'Dynamic' 'Assets' 'Management'"
        ]);
    })
    ->Default(function () {
        part("admin/system/uploads");
    })
    ->Handle();
?>
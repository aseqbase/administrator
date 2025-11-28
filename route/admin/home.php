<?php
(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view(\_::$Front->DefaultViewName, ["Name" => "admin/home"]);
    })->Handle();
?>
<?php
(new Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view(\_::$Config->DefaultViewName, ["Name" => "admin/home"]);
    })->Handle();
?>
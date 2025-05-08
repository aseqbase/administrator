<?php
(new MiMFa\Library\Router())
->if(auth(\_::$Config->AdminAccess))
    ->Get(function () {
        view(\_::$Config->DefaultViewName, ["Name" => "admin/home"]);
    })->Handle();
?>
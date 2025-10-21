<?php
(new Router())
->if(\_::$User->GetAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view(\_::$Front->DefaultViewName, ["Name" => "admin/home"]);
    })->Handle();
?>
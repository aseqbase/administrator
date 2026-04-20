<?php
use MiMFa\Module\Table;
$data = $data??[];
$routeHandler = function ($data) {
    module("Table");
    $module = new Table(table("Session"));
    $module->KeyColumns = ["Ip"];
    $module->IncludeColumns = ["Ip", "Key"];
    $module->AllowDataTranslation = false;
    $module->AllowServerSide = true;
    $module->Updatable = true;
    $module->ClearAccess = \_::$User->AdminAccess;
    $module->UpdateAccess = \_::$User->AdminAccess;
    $module->ModifyAccess = \_::$User->SuperAccess;
    pod($module, $data);
    return $module->ToString();
};

(new Router())
    ->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "clock",
            "Title" => "'Sessions' Management"
        ]);
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();
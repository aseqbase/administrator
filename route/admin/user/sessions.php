<?php
use MiMFa\Module\Table;
$data = $data ?? [];
$routeHandler = function () use ($data) {
    module("Table");
    $module = new Table(table("Session"));
    $module->KeyColumns = ["Ip"];
    $module->IncludeColumns = ["Ip", "Key"];
    $module->AllowDataTranslation = false;
    $module->AllowServerSide = true;
    $module->Updatable = true;
    $module->UpdateAccess = \_::$User->AdminAccess;
    $module->ModifyAccess = \_::$User->SuperAccess;
    pod($module, $data);
    return $module->ToString();
};

(new Router())
    ->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "clock",
            "Title" => "'Sessions' Management"
        ]);
    })
    ->Default(fn() => response($routeHandler()))
    ->Handle();
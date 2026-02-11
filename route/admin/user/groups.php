<?php
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
$data = $data ?? [];
$routeHandler = function () use ($data) {
    module("Table");
    $module = new Table(\_::$User->GroupDataTable);
    $module->SelectCondition = "Access<=" . \_::$User->GetAccess();
    $module->KeyColumns = ["Title"];
    $module->ExcludeColumns = ["MetaData"];
    $module->Updatable = true;
    $module->AllowServerSide = true;
    $module->UpdateAccess = \_::$User->AdminAccess;
    $module->CekkValue = [
        "CreateTime" => fn($v) => Convert::ToShownDateTimeString($v),
        "UpdateTime" => fn($v) => Convert::ToShownDateTimeString($v)
    ];
    $module->CellsTypes = [
        "Id" => "number",
        "Name" => "text",
        "Title" => "text",
        "Image" => "Image",
        "Description" => "texts",
        "Access" => function () {
            $std = new stdClass();
            $std->Type = "number";
            $std->Attributes = ["min" => \_::$User->BanAccess, "max" => \_::$User->GetAccess()];
            return $std;
        },
        "Status" => [1 => "Activated", 0 => "Undifined", -1 => "Blocked"],
        "MetaData" => "json"
    ];
    pod($module, $data);
    return $module->ToString();
};

(new Router())
    ->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "user-group",
            "Title" => "User Groups Management"
        ]);
    })
    ->Default(fn() => response($routeHandler()))
    ->Handle();
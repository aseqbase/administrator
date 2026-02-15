<?php
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
$data = $data ?? [];
$routeHandler = function () use ($data) {
    auth(\_::$User->AdminAccess);
    module("Table");
    $module = new Table(table("Tag"));
    $module->KeyColumns = ["Name", "Title"];
    $module->ExcludeColumns = ["MetaData"];
    $module->AllowDataTranslation = false;
    $module->AllowServerSide = true;
    $module->Updatable = true;
    $module->UpdateAccess = \_::$User->AdminAccess;
    $module->CellsValues = [
        "Name" => function ($v, $k, $r) {
            return \MiMFa\Library\Struct::Link("\${{$v}}", \_::$Address->TagRootUrlPath . $r["Id"], ["target" => "blank"]);
        },
        "CreateTime" => fn($v) => Convert::ToShownDateTimeString($v),
        "UpdateTime" => fn($v) => Convert::ToShownDateTimeString($v)
    ];
    $module->CellsTypes = [
        "Id" => "number",
        "Name" => "text",
        "Title" => "text",
        "Description" => "texts",
        "UpdateTime" => function ($t, $v) {
            $std = new stdClass();
            $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : "hidden";
            $std->Value = Convert::ToDateTimeString();
            return $std;
        },
        "CreateTime" => function ($t, $v) {
            return \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : (isValid($v) ? "hidden" : false);
        },
        "MetaData" => "json"
    ];
    pod($module, $data);
    return $module->ToString();
};

(new Router())
    ->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "tags",
            "Title" => "Tags Management"
        ]);
    })
    ->Default(fn() => response($routeHandler()))
    ->Handle();
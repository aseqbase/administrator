<?php
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
$data = $data ?? [];
$routeHandler = function () use ($data) {
    module("Table");
    $module = new Table(\_::$User->DataTable);
    $table1 = \_::$User->GroupDataTable->Name;
    $module->SelectQuery = "
    SELECT A.{$module->KeyColumn}, B.Title AS 'Group', A.Signature, A.Image, A.Name, A.Bio, A.Contact, A.Email, A.Status, A.CreateTime
    FROM {$module->DataTable->Name} AS A
    LEFT OUTER JOIN $table1 AS B ON A.GroupId=B.Id
    WHERE B.Access<=" . \_::$User->GetAccess();
    $module->KeyColumns = ["Name", "Signature"];
    $module->ExcludeColumns = ["MetaData"];
    $module->Updatable = true;
    $module->AllowServerSide = true;
    $module->UpdateAccess = \_::$User->AdminAccess;
    $module->CellValue = [
        "CreateTime" => fn($v) => Convert::ToShownDateTimeString($v)
    ];
    $module->CellsTypes = [
        "Id" => "number",
        "GroupId" => function () {
            $std = new stdClass();
            $std->Title = "Group";
            $std->Type = "select";
            $std->Options = table("UserGroup")->SelectPairs("Id", "Title", "Access<=" . \_::$User->GetAccess());
            return $std;
        },
        "Name" => "text",
        "Image" => "image",
        "Bio" => "texts",
        "Email" => "email",
        "Signature" => "text",
        "Password" => "password",
        "FirstName" => "text",
        "MiddleName" => "text",
        "LastName" => "text",
        "Gender" => "enum",
        "Contact" => "tel",
        "Organization" => "text",
        "Address" => "text",
        "Path" => "text",
        "Status" => [0 => "Deactivated", 1 => "Activated", -1 => "Blocked"],
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
            "Image" => "user",
            "Title" => "Users Management"
        ]);
    })
    ->Default(fn() => response($routeHandler()))
    ->Handle();
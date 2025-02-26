<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$mod = new Table(table("Tag"));
$mod->KeyColumns = ["Name" , "Title" ];
$mod->ExcludeColumns = ["MetaData" ];
$mod->AllowServerSide = true;
$mod->Updatable = true;
$mod->UpdateAccess = \_::$Config->AdminAccess;
$mod->CellsTypes = [
    "Id" =>"number",
    "Name" =>"string",
    "Title" =>"string",
    "Description" =>"strings",
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = auth(\_::$Config->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return auth(\_::$Config->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
];
$mod->Render();
?>
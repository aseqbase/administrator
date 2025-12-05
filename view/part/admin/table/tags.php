<?php
auth(\_::$User->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Tag"));
$module->KeyColumns = ["Name" , "Title" ];
$module->ExcludeColumns = ["MetaData" ];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->CellsValues = [
    "Name"=>function($v, $k, $r){
        return \MiMFa\Library\Struct::Link($v,\_::$Router->TagRoot.$r["Id"], ["target"=>"blank"]);
    },
    "CreateTime"=>fn($v)=> Convert::ToShownDateTimeString($v),
    "UpdateTime"=>fn($v)=> Convert::ToShownDateTimeString($v)
];
$module->CellsTypes = [
    "Id" =>"number",
    "Name" =>"string",
    "Title" =>"string",
    "Description" =>"strings",
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return \_::$User->HasAccess(\_::$User->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
];
pod($module, $data);
$module->Render();
?>
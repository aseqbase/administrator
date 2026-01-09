<?php
auth(\_::$User->AdminAccess);

use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table(\_::$User->GroupDataTable);
$module->SelectCondition = "Access<=".\_::$User->GetAccess();
$module->KeyColumns = ["Title" ];
$module->ExcludeColumns = ["MetaData" ];
$module->Updatable = true;
$module->AllowServerSide = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->CekkValue = [
    "CreateTime"=>fn($v)=> Convert::ToShownDateTimeString($v),
    "UpdateTime"=>fn($v)=> Convert::ToShownDateTimeString($v)
];
$module->CellsTypes = [
    "Id" =>"number",
    "Name" =>"string",
    "Title" =>"string",
    "Image" =>"Image" ,
    "Description" =>"strings",
    "Access" =>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$User->BanAccess,"max"=>\_::$User->GetAccess()];
        return $std;
    },
    "Status" =>[1=>"Activated",0=>"Undifined",-1=>"Blocked"],
    "MetaData" => "json"
];
pod($module, $data);
$module->Render();
?>
<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$module = new Table(\_::$User->GroupDataTable);
$module->SelectCondition = "Access<=".\_::$User->Access();
$module->KeyColumns = ["Title" ];
$module->ExcludeColumns = ["Id" , "Name" , "MetaData" ];
$module->Updatable = true;
$module->AllowServerSide = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsTypes = [
    "Id" =>"number",
    "Name" =>"string",
    "Title" =>"string",
    "Image" =>"Image" ,
    "Description" =>"strings",
    "Access" =>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$User->Access()];
        return $std;
    },
    "Status" =>[-1=>"Blocked",0=>"Undifined",1=>"Activated"],
    "MetaData" => "json"
];
swap($module, $data);
$module->Render();
?>
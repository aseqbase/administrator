<?php
auth(\_::$User->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$module = new Table(\_::$User->GroupDataTable);
$module->SelectCondition = "Access<=".\_::$User->GetAccess();
$module->KeyColumns = ["Title" ];
$module->ExcludeColumns = ["Id" , "Name" , "MetaData" ];
$module->Updatable = true;
$module->AllowServerSide = true;
$module->UpdateAccess = \_::$User->AdminAccess;
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
    "Status" =>[-1=>"Blocked",0=>"Undifined",1=>"Activated"],
    "MetaData" => "json"
];
pod($module, $data);
$module->Render();
?>
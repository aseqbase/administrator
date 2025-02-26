<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$mod = new Table(\_::$Back->User->GroupDataTable);
$mod->KeyColumns = ["Title" ];
$mod->ExcludeColumns = ["Id" , "Name" , "MetaData" ];
$mod->Updatable = true;
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$Config->AdminAccess;
$mod->CellsTypes = [
    "Id" =>"number",
    "Name" =>"string",
    "Title" =>"string",
    "Image" =>"Image" ,
    "Description" =>"strings",
    "Access" =>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$Config->UserAccess];
        return $std;
    },
    "Status" =>[-1=>"Blocked",0=>"Undifined",1=>"Activated"],
    "MetaData" => "json"
];
$mod->Render();
?>
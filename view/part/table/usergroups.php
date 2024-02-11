<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."UserGroup");
$mod->RowLabelsKeys = ["Title"];
$mod->ExcludeColumnKeys = ["ID", "Name", "MetaData"];
$mod->Updatable = true;
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellTypes = [
    "ID"=>"number",
    "Access"=>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$CONFIG->BanAccess,"max"=>\_::$CONFIG->UserAccess];
        return $std;
    },
    "Status"=>[-1=>"Blocked",0=>"Undifined",1=>"Activated"],
    "MetaData"=> "json"
];
$mod->Draw();
?>
<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."Group");
$mod->RowLabelsKeys = ["Name", "Title"];
$mod->ExcludeColumnKeys = ["ID", "Access", "MetaData"];
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellTypes = [
    "ID"=>"number",
    "Status"=>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
    "Image"=>"image",
    "Description"=>"strings",
    "MetaData"=>"json"
    ];
$mod->Draw();
?>
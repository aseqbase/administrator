<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."UserGroup");
$mod->RowLabelsKeys = ["Name"];
$mod->ExcludeColumnKeys = ["ID", "CreateTime", "UpdateTime", "MetaData"];
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$std = new stdClass();
$std->Type="number";
$std->Attributes=["min"=>\_::$CONFIG->BanAccess,"max"=>\_::$CONFIG->UserAccess];
$mod->CellTypes = [
    "ID"=>"number",
    "Access"=>$std,
    "Status"=>[-1=>"Blocked",0=>"Undifined",1=>"Activated"],
    "MetaData"=>"json"
    ];
$mod->Draw();
?>
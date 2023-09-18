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
$std->Type="range";
$std->Attributes=["min"=>0,"max"=>9];
$stdut = new stdClass();
$stdut->Type="datetime";
$stdut->Value = (new DateTime())->format('Y-m-d H:i:s');
$mod->CellTypes = [
    "ID"=>"number",
    "Access"=>$std,
    "Status"=>[-1=>"Blocked",0=>"Undifined",1=>"Activated"],
    "MetaData"=>"json",
    "CreateTime"=>"datetime",
    "UpdateTime"=>$stdut
    ];
$mod->Draw();
?>
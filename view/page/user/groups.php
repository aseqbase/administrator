<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
use MiMFa\Library\DataBase;
echo "<div class='page'>";
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."UserGroup");
$mod->RowLabelsKeys = ["ID"];
$mod->ExcludeColumnKeys = ["CreateTime", "UpdateTime", "MetaData"];
$std = new stdClass();
$std->Type="range";
$std->Attributes=["min"=>0,"max"=>9];
$mod->Updatable = true;$mod->CellTypes = [
    "ID"=>"number",
    "Access"=>$std,
    "Status"=>[-1=>"Blocked",0=>"Undifined",1=>"Activated"],
    "MetaData"=>"json",
    "CreateTime"=>"datetime",
    "UpdateTime"=>"datetime"
    ];
$mod->Draw();
echo "</div>";
?>
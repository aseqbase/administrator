<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."Content");
$mod->RowLabelsKeys = ["Name", "Title"];
$mod->ExcludeColumnKeys = ["ID", "Access", "MetaData"];
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$stdut = new stdClass();
$stdut->Type="hidden";
$stdut->Value = (new DateTime())->format('Y-m-d H:i:s');
$mod->CellTypes = [
    "ID"=>"number",
    "Gender"=>["Male"=>"Male","Female"=>"Female","X"=>"X"],
    "Status"=>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
    "Image"=>"image",
    "Bio"=>"strings",
    "Contact"=>"tel",
    "Email"=>"email",
    "Password"=>"password",
    "MetaData"=>"json",
    "CreateTime"=>"datetime",
    "UpdateTime"=>$stdut
    ];
$mod->Draw();
?>
<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
use MiMFa\Library\DataBase;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."User");
$mod->RowLabelsKeys = ["Name", "Signature"];
$mod->ExcludeColumnKeys = ["ID", "MetaData"];
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$stdut = new stdClass();
$stdut->Type="hidden";
$stdut->Value = (new DateTime())->format('Y-m-d H:i:s');
$mod->CellTypes = [
    "ID"=>"number",
    "GroupID"=>DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."UserGroup","`ID`", "`Title`"),
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
<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
use MiMFa\Library\DataBase;
echo "<div class='page'>";
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."User");
$mod->RowLabelsKeys = ["ID", "Signature", "Email"];
$mod->ExcludeColumnKeys = ["CreateTime", "UpdateTime", "MetaData"];
$mod->Updatable = true;
$mod->CellTypes = [
    "ID"=>"number",
    "GroupID"=>DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."UserGroup","`ID`", "`Title`"),
    "Gender"=>["Male"=>"Male","Female"=>"Female","X"=>"X (Transexual)"],
    "Status"=>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
    "Image"=>"image",
    "Bio"=>"strings",
    "Contact"=>"tel",
    "Email"=>"email",
    "Password"=>"password",
    "MetaData"=>"json",
    "CreateTime"=>"datetime",
    "UpdateTime"=>"datetime"
    ];
$mod->Draw();
echo "</div>";
?>
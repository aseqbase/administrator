<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
use MiMFa\Library\DataBase;
echo "<div class='page'>";
MODULE("Table");
$mod = new Table(DataBase::DoSelect(\_::$CONFIG->DataBasePrefix."UserGroup"));
$mod->RowLabelsKeys = ["ID"];
$mod->ExcludeColumnKeys = ["CreateTime", "UpdateTime", "MetaData"];
$mod->Changeable = true;
$mod->Draw();
echo "</div>";
?>
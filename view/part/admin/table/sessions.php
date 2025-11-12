<?php
auth(\_::$User->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Session"));
$module->KeyColumns = ["Ip"];
$module->IncludeColumns = ["Ip", "Key"];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->ModifyAccess = \_::$User->SuperAccess;
pod($module, $data);
$module->Render();
?>
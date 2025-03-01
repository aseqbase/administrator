<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Translate_Lexicon"));
$module->KeyColumns = ["KeyCode"];
$module->KeyColumn = "KeyCode";
$module->Updatable = \Req::Receive("update");
$module->AllowServerSide = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsTypes = [
    "KeyCode"=> "text",
    "ValueOptions"=> "json"
];
$module->Render();
?>
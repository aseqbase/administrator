<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$mod = new Table(table("Translate_Lexicon"));
$mod->KeyColumns = ["KeyCode"];
$mod->KeyColumn = "KeyCode";
$mod->Updatable = \Req::Receive("update");
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$Config->AdminAccess;
$mod->CellsTypes = [
    "KeyCode"=> "text",
    "ValueOptions"=> "json"
];
$mod->Render();
?>
<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Translate_Lexicon", prefix:false));
$module->KeyColumns = ["KeyCode"];
$module->KeyColumn = "KeyCode";
$module->AllowServerSide = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsTypes = [
    "KeyCode"=> "text",
    "ValueOptions"=> "json"
];
swap($module, $data);
$module->Render();
?>
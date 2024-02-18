<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table("Translate_Lexicon");
$mod->KeyColumns = ["KeyCode"];
$mod->KeyColumn = "KeyCode";
$mod->Updatable = RECEIVE("update");
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellsTypes = [
    "KeyCode"=> "text",
    "ValueOptions"=> "json"
];
$mod->Draw();
?>
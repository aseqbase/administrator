<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table("Translate_Lexicon");
$mod->RowLabelsKeys = ["KeyCode"];
$mod->ColumnKey = "KeyCode";
$mod->Updatable = RECEIVE("update");
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellTypes = [
    "KeyCode"=> "text",
    "ValueOptions"=> "json"
];
$mod->Draw();
?>
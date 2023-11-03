<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table("Translate_Lexicon");
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->ModifyAccess = \_::$CONFIG->AdminAccess;
$mod->AddAccess = \_::$CONFIG->AdminAccess;
$mod->RemoveAccess = \_::$CONFIG->AdminAccess;
$mod->Draw();
?>
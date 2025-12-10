<?php
auth(\_::$User->AdminAccess);

use MiMFa\Library\Convert;
module("Table");
$table = table("Translate_Lexicon", prefix: false);
$langs = \_::$Front->Translate->GetLanguages();
$module = new \MiMFa\Module\Table($table);
$module->SelectQuery = $table->SelectQuery(join(",",["Id","KeyCode", ...loop($langs, fn($v, $k)=>"ValueOptions AS '".strtoupper($k)."'")]));
$module->ExcludeColumns = ["Id"];
$module->AllowLabelTranslation = false;
$module->AllowServerSide = true;
$module->Controlable = true;
$module->AddAccess = 
$module->RemoveAccess = 
$module->ModifyAccess = 
$module->DuplicateAccess = 
$module->UpdateAccess = \_::$User->AdminAccess;

foreach ($langs as $k=>$value)
    $module->CellsValues[$k] = function ($v) use ($k) {
        return getBetween(Convert::FromJson($v), $k, \_::$Front->Translate->Language??"x", "x");
    };
$module->CellsTypes = [
    "Id" => "hidden",
    "KeyCode" => "text",
    "ValueOptions" => "json"
];
pod($module, $data);
$module->Render();
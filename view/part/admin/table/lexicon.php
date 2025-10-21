<?php
inspect(\_::$User->AdminAccess);

use MiMFa\Library\Convert;
module("Table");
$table = table("Translate_Lexicon", prefix: false);
$langs = \_::$Back->Translate->GetLanguages();
$module = new \MiMFa\Module\Table($table);
$module->SelectQuery = $table->SelectQuery(join(",",["KeyCode", ...loop($langs, fn($v, $k)=>"ValueOptions AS '".strtoupper($k)."'")]));
$module->KeyColumn = "KeyCode";
$module->AllowLabelTranslation = false;
$module->AllowServerSide = true;
$module->AddAccess = 
$module->DuplicateAccess = false;
$module->UpdateAccess = \_::$User->AdminAccess;

foreach ($langs as $k=>$value)
    $module->CellsValues[$k] = function ($v) use ($k) {
        return getBetween(Convert::FromJson($v), $k, \_::$Back->Translate->Language??"x", "x");
    };

$module->CellsTypes = [
    "KeyCode" => "text",
    "ValueOptions" => "json"
];
swap($module, $data);
$module->Render();
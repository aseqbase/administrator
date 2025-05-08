<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Payment"));
$module->SelectCondition = "ORDER BY `CreateTime` DESC";
$module->KeyColumns = ['Value', "Transaction"];
$module->IncludeColumns = ['TId', 'Value', 'Unit', 'Source', 'Destination', 'Transaction' , 'CreateTime'];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->ModifyAccess = 
$module->DeleteAccess = \_::$Config->SuperAccess;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsTypes = [
    "Id" =>auth(\_::$Config->SuperAccess)?"disabled":false,
    'TId'=>"string",
    'Relation'=>"",
    'Source'=>"string",
    'SourceEmail'=>"email",
    'SourceContent'=>"string",
    'SourcePath'=>"string",
    'Value'=>"string",
    'Unit'=>"string",
    'Network'=>"string",
    'Transaction'=>"string",
    'Identifier'=>"string",
    'Destination'=>"string",
    'DestinationEmail'=>"email",
    'DestinationContent'=>"string",
    'DestinationPath'=>"string",
    'Others'=>"string",
    "CreateTime" => function($t, $v){
        return auth(\_::$Config->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
    ];
swap($module, $data);
$module->Render();
?>
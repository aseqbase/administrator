<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Payment"));
$module->SelectCondition = "ORDER BY `CreateTime` DESC";
$module->KeyColumns = ['Value', "Transaction"];
$module->IncludeColumns = ['TId', 'Value', 'Unit', 'Source', 'Destination', 'Transaction' , 'CreateTime'];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsTypes = [
    "Id" =>auth(\_::$Config->SuperAccess)?"disabled":false,
    "CreateTime" => function($t, $v){
        return auth(\_::$Config->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
    ];
swap($module, $data);
$module->Render();
?>
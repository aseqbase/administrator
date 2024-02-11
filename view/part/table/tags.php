<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."Tag");
$mod->RowLabelsKeys = ["Name", "Title"];
$mod->ExcludeColumnKeys = ["ID", "MetaData"];
$mod->AllowServerSide = true;
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellTypes = [
    "Name"=>"string",
    "Title"=>"string",
    "Description"=>"strings",
    "UpdateTime"=>function($t, $v){
        $std = new stdClass();
        $std->Type = getAccess(\_::$CONFIG->SuperAccess)?"calendar":"hidden";
        $std->Value = \_::$CONFIG->GetFormattedDateTime();
        return $std;
    },
    "CreateTime"=> function($t, $v){
        return getAccess(\_::$CONFIG->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData"=>"json"
    ];
$mod->Draw();
?>
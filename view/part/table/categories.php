<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Library\DataBase;
use MiMFa\Module\Table;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."Category");
$mod->SelectQuery = "
    SELECT A.{$mod->ColumnKey}, A.Name, B.Name AS 'Parent', A.Image, A.Title, A.Description, A.Status, A.Access, A.UpdateTime
    FROM {$mod->Table} AS A
    LEFT OUTER JOIN {$mod->Table} AS B ON A.ParentID=B.ID
";
$mod->RowLabelsKeys = ["Image", "Name", "Title"];
$mod->ExcludeColumnKeys = ["Content", "Access", "MetaData", "CreateTime"];
$mod->Updatable = true;
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellTypes = [
    "ID"=>"number",
    "ParentID" => function(){
        $std = new stdClass();
        $std->Title = "Parent";
        $std->Description = "The parent category which is related";
        $std->Type = "select";
        $std->Options = DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."Category", "`ID`", "`Name`");
        return $std;
    },
    "Name"=>"string",
    "Title"=>"string",
    "Description"=>"strings",
    "Image"=>"image",
    "Access"=>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$CONFIG->BanAccess,"max"=>\_::$CONFIG->UserAccess];
        return $std;
    },
    "Status"=>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
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
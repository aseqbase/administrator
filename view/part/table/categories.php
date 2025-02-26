<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$mod = new Table(table("Category"));
$mod->SelectQuery = "
    SELECT A.{$mod->KeyColumn}, A.Name, B.Name AS 'Parent', A.Image, A.Title, A.Description, A.Status, A.Access, A.UpdateTime
    FROM {$mod->DataTable->Name} AS A
    LEFT OUTER JOIN {$mod->DataTable->Name} AS B ON A.ParentId=B.Id
    ORDER BY A.ParentId ASC
";
$mod->KeyColumns = ["Image" , "Name" , "Title" ];
$mod->ExcludeColumns = ["Content" , "Access" , "MetaData" , "CreateTime" ];
$mod->AllowServerSide = true;
$mod->Updatable = true;
$mod->UpdateAccess = \_::$Config->AdminAccess;
$mod->CellsTypes = [
    "Id" =>"number",
     "ParentId" => function(){
        $std = new stdClass();
        $std->Title = "Parent";
        $std->Description = "The parent category which is related";
        $std->Type = "select";
        $std->Options = table("Category")->DoSelectPairs("`Id`", "`Name`", "TRUE ORDER BY  `ParentId` ASC");
        return $std;
    },
    "Name" =>"string",
    "Image" =>"Image" ,
    "Title" =>"string",
    "Description" =>"strings",
    "Access" =>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$Config->UserAccess];
        return $std;
    },
    "Status" =>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = auth(\_::$Config->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return auth(\_::$Config->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
];
$mod->Render();
?>
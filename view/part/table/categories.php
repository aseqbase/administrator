<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Category"));
$module->SelectQuery = "
    SELECT A.{$module->KeyColumn}, A.Name, B.Name AS 'Parent', A.Image, A.Title, A.Description, A.Status, A.Access, A.UpdateTime
    FROM {$module->DataTable->Name} AS A
    LEFT OUTER JOIN {$module->DataTable->Name} AS B ON A.ParentId=B.Id
    ORDER BY A.ParentId ASC
";
$module->KeyColumns = ["Image" , "Name" , "Title" ];
$module->ExcludeColumns = ["Content" , "Access" , "MetaData" , "CreateTime" ];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsTypes = [
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
$module->Render();
?>
<?php
auth(\_::$User->AdminAccess);
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
$module->KeyColumns = ["Name" , "Title"];
$module->ExcludeColumns = ["Content" , "Access" , "MetaData" , "CreateTime" ];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->CellsValues = [
    "Name"=>function($v, $k, $r){
        return \MiMFa\Library\Struct::Link($v,\_::$Address->CategoryRoot.$r["Id"], ["target"=>"blank"]);
    }
];
$module->CellsTypes = [
    "Id" =>"number",
     "ParentId" => function(){
        $std = new stdClass();
        $std->Title = "Parent";
        $std->Description = "The parent category which is related";
        $std->Type = "select";
        $std->Options = table("Category")->SelectPairs("`Id`", "`Name`", "TRUE ORDER BY  `ParentId` ASC");
        return $std;
    },
    "Name" =>"string",
    "Image" =>"Image" ,
    "Title" =>"string",
    "Description" =>"strings",
    "Access" =>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$User->BanAccess,"max"=>\_::$User->SuperAccess];
        return $std;
    },
    "Status" =>[-1=>"Unpublished",0=>"Drafted",1=>"Published"],
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = \_::$User->GetAccess(\_::$User->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return \_::$User->GetAccess(\_::$User->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
];
pod($module, $data);
$module->Render();
?>
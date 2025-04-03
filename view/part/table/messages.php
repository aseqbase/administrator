<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\Html;
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Comment"));
$module->SelectQuery = "
    SELECT *
    FROM {$module->DataTable->Name}
    WHERE Relation REGEXP '^[^0-9]'
    ORDER BY `CreateTime` DESC
";
$module->KeyColumns = ["Subject" ];
$module->IncludeColumns = ['Name', 'Relation', 'Subject', 'Content', 'Contact', 'Status' , 'Access' , 'CreateTime'];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsValues = [
    "Relation" =>fn($v)=> \Req::$Host.$v,
    "Contact" =>fn($v)=> Html::Link($v, "mailto:$v")
];
$module->CellsTypes = [
    "Id" =>auth(\_::$Config->SuperAccess)?"disabled":false,
    "UserId" =>"number",
    "Name" =>"string",
    "Subject" =>"string",
    "Content" =>"Content" ,
    "Contact"=>"Email",
    "Attach" =>"json",
    "Status" =>[-1=>"Unpublished",0=>"Drafted",1=>"Published"],
    "GroupId" => function(){
        $std = new stdClass();
        $std->Title = "User Group Access";
        $std->Type = "select";
        $std->Options = table("UserGroup")->DoSelectPairs("Id" , "Title" );
        return $std;
    },
    "Access" =>function(){
        $std = new stdClass();
        $std->Title = "Minimum Access";
        $std->Type="number";
        $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$Config->SuperAccess];
        return $std;
    },
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
swap($module, $data);
$module->Render();
?>
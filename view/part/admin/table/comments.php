<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\Html;
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Comment"));
$contentT = table("Content" )->Name;
$module->SelectQuery = "
    SELECT A.{$module->KeyColumn}, C.Title AS 'Title' , C.Id AS 'Post', A.Subject AS 'Subject', A.Content AS 'Content', A.Name AS 'Author', A.Status, A.Access, A.CreateTime, A.UpdateTime
    FROM {$module->DataTable->Name} AS A
    LEFT OUTER JOIN $contentT AS C ON A.Relation=C.Id
    ORDER BY A.`UpdateTime` DESC
";
$module->KeyColumns = ["Subject" ];
$module->IncludeColumns = ['Title' , 'Author', 'Subject' , 'Content' , 'Status' , 'Access' , 'CreateTime' , 'UpdateTime' ];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsValues = [
    "Title" =>function($v, $k, $r){
        return $r["Post"]?Html::Link($v, \_::$Address->ContentRoute. $r["Post"],["target"=>"_blank"]):null;
    },
    "Contact" =>fn($v)=> Html::Link($v, "mailto:$v")
];
$module->CellsTypes = [
    "Id" =>auth(\_::$Config->SuperAccess)?"disabled":false,
    "Relation" =>"string",
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
        $std->Options = table("UserGroup")->SelectPairs("Id" , "Title" );
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
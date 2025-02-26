<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\HTML;
use MiMFa\Module\Table;
module("Table");
$mod = new Table(table("Comment"));
$contentT = table("Content" )->Name;
$mod->SelectQuery = "
    SELECT A.{$mod->KeyColumn}, C.Title AS 'Title' , C.Id AS 'Post', A.Subject AS 'Subject' , A.Content AS 'Content' , A.Name AS 'author', A.Status, A.Access, A.CreateTime, A.UpdateTime
    FROM {$mod->DataTable->Name} AS A
    LEFT OUTER JOIN $contentT AS C ON A.Relation=C.Id
    ORDER BY A.`UpdateTime` DESC
";
$mod->KeyColumns = ["Subject" ];
$mod->IncludeColumns = ['Title' , 'author', 'Subject' , 'Content' , 'Status' , 'Access' , 'CreateTime' , 'UpdateTime' ];
$mod->AllowServerSide = true;
$mod->Updatable = true;
$mod->UpdateAccess = \_::$Config->AdminAccess;
$mod->CellsValues = [
    "Title" =>function($v, $k, $r){
        return Html::Link($v, \_::$Address->ContentPath. $r["Post"],["target"=>"_blank"]);
    }
];
$mod->CellsTypes = [
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
        $std->Options = table("UserGroup")->DoSelectPairs("Id" , "Title" );
        return $std;
    },
    "Access" =>function(){
        $std = new stdClass();
        $std->Title = "Minimum Access";
        $std->Type="number";
        $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$Config->UserAccess];
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
$mod->Render();
?>
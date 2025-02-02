<?php
ACCESS(\_::$CONFIG->AdminAccess);

use MiMFa\Library\Convert;
use MiMFa\Library\DataBase;
use MiMFa\Library\HTML;
use MiMFa\Module\Table;
LIBRARY("Query");
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."Comment");
$contentT = \_::$CONFIG->DataBasePrefix."Content";
$mod->SelectQuery = "
    SELECT A.{$mod->KeyColumn}, C.Title AS 'Title', C.ID AS 'Post', A.Subject AS 'Subject', A.Content AS 'Content', A.Name AS 'Author', A.Status, A.Access, A.CreateTime, A.UpdateTime
    FROM {$mod->Table} AS A
    LEFT OUTER JOIN $contentT AS C ON A.Relation=C.ID
    ORDER BY A.`UpdateTime` DESC
";
$mod->KeyColumns = ["Subject"];
$mod->IncludeColumns = ['Title', 'Author', 'Subject', 'Content', 'Status', 'Access', 'CreateTime', 'UpdateTime'];
$mod->AllowServerSide = true;
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellsValues = [
    "Title"=>function($v, $k, $r){
        return HTML::Link($v, "/post/".$r["Post"],["target"=>"_blank"]);
    }
];
$mod->CellsTypes = [
    "ID"=>getAccess(\_::$CONFIG->SuperAccess)?"disabled":false,
    "Relation"=>"string",
    "UserID"=>"number",
    "Name"=>"string",
    "Subject"=>"string",
    "Content"=>"content",
    "Contact"=>"email",
    "Attach"=>"json",
    "Status"=>[-1=>"Unpublished",0=>"Drafted",1=>"Published"],
    "GroupID"=> function(){
        $std = new stdClass();
        $std->Title = "User Group Access";
        $std->Type = "select";
        $std->Options = DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."UserGroup", "ID", "Title");
        return $std;
    },
    "Access"=>function(){
        $std = new stdClass();
        $std->Title = "Minimum Access";
        $std->Type="number";
        $std->Attributes=["min"=>\_::$CONFIG->BanAccess,"max"=>\_::$CONFIG->UserAccess];
        return $std;
    },
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
<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table("Content");
$table1 = \_::$Back->User->DataTable->Name;
$module->SelectQuery = "
    SELECT A.{$module->KeyColumn}, A.Type, A.Image, A.Title, A.CategoryIds AS 'Category', A.Priority, A.Status, A.Access, B.Name AS 'Author', C.Name AS 'Editor', A.CreateTime, A.UpdateTime
    FROM {$module->DataTable->Name} AS A
    LEFT OUTER JOIN $table1 AS B ON A.AuthorId=B.Id
    LEFT OUTER JOIN $table1 AS C ON A.EditorId=C.Id
    ORDER BY A.`Priority` DESC, A.`UpdateTime` DESC, A.`CreateTime` DESC
";
$module->KeyColumns = ["Image" , "Title" ];
$module->IncludeColumns = ['Type' , 'Image' , 'Title' , 'Category', 'Priority' , 'Status' , 'Access' , 'Author', 'Editor', 'CreateTime' , 'UpdateTime' ];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$users = table("User")->SelectPairs("Id" , "Name" );
$module->CellsValues = [
    "Title"=>function($v, $k, $r){
        return \MiMFa\Library\Html::Link($v,\_::$Address->ContentRoute.$r["Id"], ["target"=>"blank"]);
    },
    "Category"=>function($v, $k, $r){
        $val = trim(\_::$Back->Query->GetCategoryRoute(first(Convert::FromJson($v)))??"", "/\\");
        if(isValid($val)) return \MiMFa\Library\Html::Link($val,\_::$Address->CategoryRoute.$val, ["target"=>"blank"]);
        return $v;
    }
];
$module->CellsTypes = [
    "Id" =>auth(\_::$Config->SuperAccess)?"disabled":false,
    "Name" =>"string",
    "Type" =>"enum",
    "Title" =>"string",
    "Image" =>"image" ,
    "Description" =>"strings",
    "Content" =>"content" ,
    "CategoryIds" => function(){
        $std = new stdClass();
        $std->Title = "Categories";
        $std->Type = "array";
        $std->Options = [
            "Type" =>"select",
            "Key" =>"CategoryIds" ,
            "Options"=>table("Category")->SelectPairs("`Id`", "`Name`", "ORDER BY `ParentId` ASC")
        ];
        return $std;
    },
    "TagIds" =>function(){
        $std = new stdClass();
        $std->Title = "Tags";
        $std->Type = "array";
        $std->Options = [
            "Type" =>"select",
            "Key" =>"TagIds" ,
            "Options"=>table("Tag")->SelectPairs("`Id`", "`Name`")
        ];
        return $std;
    },
    "Status" =>[-1=>"Unpublished",0=>"Drafted",1=>"Published"],
    "Access" =>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$Config->SuperAccess];
        return $std;
    },
    "Attach" =>"json",
    "Path" =>"string",
    "Priority" =>"number",
    "AuthorId" =>function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "Author";
        $std->Type = auth(\_::$Config->SuperAccess)?"select":"hidden";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$Back->User->Id;
        return $std;
    },
    "EditorId" =>function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "Editor";
        $std->Type = auth(\_::$Config->SuperAccess)?"select":"hidden";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$Back->User->Id;
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
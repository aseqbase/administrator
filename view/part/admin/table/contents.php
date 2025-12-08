<?php
auth(\_::$User->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table("Content");
$table1 = \_::$User->DataTable->Name;
$module->SelectQuery = "
    SELECT A.{$module->KeyColumn}, A.Type, A.Image, A.Title, A.CategoryIds AS 'Category', A.Priority, A.Status, A.MetaData AS 'Lang', A.Access, B.Name AS 'Author', C.Name AS 'Editor', A.CreateTime, A.UpdateTime
    FROM {$module->DataTable->Name} AS A
    LEFT OUTER JOIN $table1 AS B ON A.AuthorId=B.Id
    LEFT OUTER JOIN $table1 AS C ON A.EditorId=C.Id
    ORDER BY A.Name ASC, A.Priority DESC, A.UpdateTime DESC, A.CreateTime DESC
";
$module->KeyColumns = ["Image" , "Title" ];
$module->IncludeColumns = ['Type' , 'Image' , 'Title' , 'Category', 'Priority' , 'Status' , 'Lang' , 'Access' , 'Author', 'Editor', 'CreateTime' , 'UpdateTime' ];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$users = table("User")->SelectPairs("Id" , "Name" );
$langs = \_::$Front->Translate->GetLanguages();
$module->CellsValues = [
    "Title"=>function($v, $k, $r){
        return \MiMFa\Library\Struct::Link($v,\_::$Address->ContentRoot.$r["Id"], ["target"=>"blank"]);
    },
    "Category"=>function($v, $k, $r){
        $val = trim(\_::$Back->Query->GetCategoryRoute(first(Convert::FromJson($v)))??"", "/\\");
        if(isValid($val)) return \MiMFa\Library\Struct::Link($val,\_::$Address->CategoryRoot.$val, ["target"=>"blank"]);
        return $v;
    },
    "Lang"=>function($v) use($langs){
        return $v?get(get($langs, get(Convert::FromJson($v), "lang")), "Title")??"Default":"Default";
    },
    "Status"=>function($v){
        return \MiMFa\Library\Struct::Span($v>0?"Published":($v<0?"Unpublished":"Drafted"));
    },
    "CreateTime"=>fn($v)=> Convert::ToShownDateTimeString($v),
    "UpdateTime"=>fn($v)=> Convert::ToShownDateTimeString($v)
];
$module->CellsTypes = [
    "Id" =>\_::$User->HasAccess(\_::$User->SuperAccess)?"disabled":false,
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
        $std->Attributes=["min"=>\_::$User->BanAccess,"max"=>\_::$User->SuperAccess];
        return $std;
    },
    "Attach" =>"json",
    "Path" =>"string",
    "Priority" =>"number",
    "AuthorId" =>function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "Author";
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess)?"select":"hidden";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$User->Id;
        return $std;
    },
    "EditorId" =>function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "Editor";
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess)?"select":"hidden";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$User->Id;
        return $std;
    },
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return \_::$User->HasAccess(\_::$User->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "json";
        if(\_::$Front->AllowTranslate && !$r["Title"] && !$r["Content"]) $std->Value = "{\"lang\":\"".\_::$Front->Translate->Language."\"}";
        return $std;
    },
    ];
pod($module, $data);
$module->Render();
?>
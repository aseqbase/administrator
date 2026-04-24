<?php

use MiMFa\Library\Revise;

\_::$User->AllowSigning = true;
\_::$Front->AllowTranslate = true;
\_::$Back->ReportError = \_::$Back->ReportError ?: E_ALL;
\_::$Back->DisplayError = \_::$Back->DisplayError ?: 1;
\_::$Back->DisplayStartupError = \_::$Back->DisplayStartupError ?: 1;
\_::$Back->DataBaseError = \_::$Back->DataBaseError ?: 1;
$dirs = array_keys(\_::$Sequence);
find($dirs, __DIR__ . DIRECTORY_SEPARATOR, index: $index);
\_::$Back->AdminOrigin = $index ?: 0;
$name = \_::$Address->Name ?? "qb";
\_::$Address->Name = (isset($_COOKIE["BASE"]) ? $_COOKIE["BASE"] : $GLOBALS["BASE"]) ?: \_::$Address->Name;
if (\_::$Back->AdminOrigin === 0) // Change temp directories to the Root sequence
    \_::$Address->TempDirectory = $dirs[1] . ltrim(\_::$Address->TempRootDirectory, DIRECTORY_SEPARATOR);
$rootPath = $dirs[\_::$Back->AdminOrigin + 1];
\_::$Address->PublicDirectory = $dirs[\_::$Back->AdminOrigin + 1] . ltrim(\_::$Address->PublicRootDirectory, DIRECTORY_SEPARATOR);
\_::$Address->AssetDirectory = $rootPath . ltrim(\_::$Address->AssetRootDirectory, DIRECTORY_SEPARATOR);

if (\_::$Back->DataBaseAddNameToPrefix)
    \_::$Back->DataBasePrefix = str_replace("{$name}_", (\_::$Address->Name ?? "qb") . "_", \_::$Back->DataBasePrefix);

if (\_::$User->HasAccess(\_::$User->AdminAccess)) {
    \_::$Front->SenderEmail = "do-not-reply@" . getUrlDomain(\_::$Address->RootUrlPath);
    \_::$Front->ReceiverEmail = "info@" . getUrlDomain(\_::$Address->RootUrlPath);
    \_::$Front->AdminMenus = array(
        "Administrator" => array("Name" => "DASHBOARD", "Path" => "/sign/dashboard", "Access" => \_::$User->AdminAccess, "Image" => "home"),
        "Administrator-Content" => array(
            "Name" => "CONTENTS",
            "Path" => "/administrator/content/contents",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage all contents in the website",
            "Image" => "th-large",
            "Items" => array(
                array("Name" => "CONTENTS", "Path" => "/administrator/content/contents", "Access" => \_::$User->AdminAccess, "Description" => "To manage website's posts and pages", "Image" => "file"),
                array("Name" => "TAGS", "Path" => "/administrator/content/tags", "Access" => \_::$User->AdminAccess, "Description" => "To manage website's tags", "Image" => "tags"),
                array("Name" => "CATEGORIES", "Path" => "/administrator/content/categories", "Access" => \_::$User->AdminAccess, "Description" => "To manage website's categories", "Image" => "code-fork"),
            )
        ),
        "Administrator-Storage" => array(
            "Name" => "STORAGES",
            "Path" => "/administrator/storage/dynamic",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage all 'files'",
            "Image" => "server",
            "Items" => array(
                array("Name" => "'UPLOADED' 'STORAGE'", "Path" => "/administrator/storage/dynamic", "Access" => \_::$User->AdminAccess, "Description" => "'Uploaded' 'files' 'management'", "Image" => "download"),
                array("Name" => "'ORGANIZED' 'STORAGE'", "Path" => "/administrator/storage/static", "Access" => \_::$User->AdminAccess, "Description" => "'Organized' 'files' 'management'", "Image" => "folder-tree"),
                array("Name" => "'TEMPORARY' 'STORAGE'", "Path" => "/administrator/storage/temp", "Access" => \_::$User->AdminAccess, "Description" => "'Temporary' 'files' 'management'", "Image" => "clock"),
                array("Name" => "'SEQUENCE' 'STORAGE'", "Path" => "/administrator/storage/sequence", "Access" => \_::$User->AdminAccess, "Description" => "'Sequence' 'files' 'management'", "Image" => "globe"),
                array("Name" => "'ROOT' 'STORAGE'", "Path" => "/administrator/storage/root", "Access" => \_::$User->SuperAccess, "Description" => "'Root' 'files' 'management'", "Image" => "folder"),
            )
        ),
        "Administrator-User" => array(
            "Name" => "USERS",
            "Path" => "/administrator/user/users",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage all interactions with the website",
            "Image" => "user",
            "Items" => array(
                array("Name" => "USERS", "Path" => "/administrator/user/users", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the website's users", "Image" => "user"),
                array("Name" => "GROUPS", "Path" => "/administrator/user/groups", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the user groups of the website", "Image" => "user-group"),
                array("Name" => "COMMENTS", "Path" => "/administrator/user/comments", "Access" => \_::$User->AdminAccess, "Description" => "To manage all received comments", "Image" => "comment"),
                array("Name" => "MESSAGES", "Path" => "/administrator/user/messages", "Access" => \_::$User->AdminAccess, "Description" => "To manage all received emails and messages", "Image" => "envelope"),
                array("Name" => "SESSIONS", "Path" => "/administrator/user/sessions", "Access" => \_::$User->AdminAccess, "Description" => "To manage all 'sessions'", "Image" => "clock"),
                array("Name" => "MANAGEMENT", "Path" => "/administrator/user/management", "Access" => \_::$User->SuperAccess, "Description" => "To config 'users'", "Image" => "user-cog")
            )
        ),
        "Administrator-System" => array(
            "Name" => "SYSTEMS",
            "Path" => "/administrator/system/information",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage the back-end website settings",
            "Image" => "cog",
            "Items" => array(
                array("Name" => "TRANSLATION", "Path" => "/administrator/system/translation", "Access" => \_::$User->AdminAccess, "Image" => "language"),
                array("Name" => "APPEARANCE", "Path" => "/administrator/system/template", "Access" => \_::$User->AdminAccess, "Image" => "eye"),
                //array("Name" => "TEMPLATES", "Path" => "/administrator/system/templates", "Access" => \_::$User->AdminAccess, "Image" => "th"),
                array("Name" => "PLUGINS", "Path" => "/administrator/system/plugins", "Access" => \_::$User->AdminAccess, "Image" => "puzzle-piece"),
                array("Name" => "MARKET", "Path" => "http://github.com/aseqbase", "Access" => \_::$User->AdminAccess, "Image" => "shopping-bag"),
                array(
                    "Name" => "INFORMATION",
                    "Path" => "/administrator/system/information",
                    "Access" => \_::$User->AdminAccess,
                    "Image" => "edit",
                    "Items" => loop(Revise::GetCategories(\_::$Front), fn($v, $k) => [
                        "Name" => $k,
                        "Path" => "/administrator/system/information?category=" . urlencode($k),
                        "Access" => \_::$User->AdminAccess,
                        "Image" => "edit"
                    ])
                ),
                array(
                    "Name" => "CONFIGURATION",
                    "Path" => "/administrator/system/configuration",
                    "Access" => \_::$User->SuperAccess,
                    "Image" => "cog",
                    "Items" => loop(Revise::GetCategories(\_::$Back), fn($v, $k) => [
                        "Name" => $k,
                        "Path" => "/administrator/system/configuration?category=" . urlencode($k),
                        "Access" => \_::$User->AdminAccess,
                        "Image" => "cog"
                    ])
                )
            )
        )
    );
    \_::$Front->AdminShortcuts = array(
        array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
        array("Name" => "CONTENTS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/content/contents", "Image" => "th-large"),
        array("Name" => "HOME", "Access" => \_::$User->AdminAccess, "Path" => "/sign/dashboard", "Image" => "home"),
        array("Name" => "USERS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/user/users", "Image" => "user"),
        array("Name" => "SYSTEMS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/system/information", "Image" => "cog"),
    );
}

\_::$Router
    ->On()->Reset()
    ->On("~administrator")->Get(function () {
        $up = "~administrator";
        $pairs = \_::$User->GroupDataTable->SelectPairs("Id", "Access", "Access>900000000");
        if (!$pairs)
            deliverError("There is not at least one administrator access group!");
        if (!\_::$User->DataTable->Exists("GroupId IN (" . join(",", loop($pairs, fn($v, $k) => $k)) . ")")) {
            if (
                \_::$User->SignUp(
                    $un = receiveGet("UserName") ?? $up,
                    $ps = receiveGet("Password") ?? randomString(24) ?? $up,
                    $em = receiveGet("Email") ?? \_::$User->GenerateEmail(fake: true),
                    groupId: receiveGet("GroupId") ?? array_key_last($pairs),
                    status: \_::$User->ActiveStatus,
                ) != false
            ) {
                view(\_::$Front->DefaultViewName, [
                    "Content" =>
                        MiMFa\Library\Struct::Heading1("'Your Administrator Account Created Successfully' " . MiMFa\Library\Struct::Icon("print", "window.print();", ["class" => "view unptintable"])) .
                        MiMFa\Library\Struct::Table([
                            ["Name", "Value", "Description"],
                            ["UserName", $un, ""],
                            ["Password", $ps, "Please change it immediately"],
                            ["Email", $em, isEmail($em) ? "" : "It is a fake email, Please change it immediately"]
                        ]) .
                        MiMFa\Library\Struct::Button("Update your profile", \_::$User->EditHandlerPath, ["class" => "Main"]) .
                        (\_::$User->SignIn($un, $ps) !== false ? MiMFa\Library\Struct::Success("You are signed in now!") : "")
                ]);
            }
        } else
            route(404);
    })
    ->if(!\_::$User->HasAccess(\_::$User->AdminAccess))
    ->On("$|administrator")->Default(fn() => view("part", ["Name" => \_::$User->InHandlerPath]))
    ->On()->Default(\_::$Router->DefaultRouteName)
    ->else()
    ->On("administrator")->Reset()->Default(\_::$Address->UrlRoute, alternative: \_::$Router->DefaultRouteName);
<?php
\_::$User->AllowSigning = true;
\_::$Back->ReportError = \_::$Back->ReportError ?: E_ALL;
\_::$Back->DisplayError = \_::$Back->DisplayError ?: 1;
\_::$Back->DisplayStartupError = \_::$Back->DisplayStartupError ?: 1;
\_::$Back->DataBaseError = \_::$Back->DataBaseError ?: 1;
\_::$Back->AdminOrigin = array_key_first(\_::$Sequence) === __DIR__ . DIRECTORY_SEPARATOR ? 0 : 1;
$name = \_::$Address->Name ?? "qb";
\_::$Address->Name = (isset($_COOKIE["BASE"]) ? $_COOKIE["BASE"] : $GLOBALS["BASE"]) ?: \_::$Address->Name;
if (\_::$Back->AdminOrigin === 0) { // Change public access directories to the Root sequence
    $rootPath = array_keys(\_::$Sequence)[\_::$Back->AdminOrigin + 1];
    \_::$Address->PublicAddress = $rootPath . ltrim(\_::$Address->PublicDirectory, DIRECTORY_SEPARATOR);
    \_::$Address->AssetAddress = $rootPath . ltrim(\_::$Address->AssetDirectory, DIRECTORY_SEPARATOR);
}

if (\_::$Back->DataBaseAddNameToPrefix)
    \_::$Back->DataBasePrefix = str_replace("{$name}_", (\_::$Address->Name ?? "qb") . "_", \_::$Back->DataBasePrefix);

if (\_::$User->HasAccess(\_::$User->AdminAccess)) {
    \_::$Front->SenderEmail = "do-not-reply@" . getDomain(\_::$Address->Root);
    \_::$Front->ReceiverEmail = "info@" . getDomain(\_::$Address->Root);
    \_::$Front->MainMenus = \_::$Front->SideMenus = array(
        "Admin-Main" => array("Name" => "DASHBOARD", "Path" => "/sign/dashboard", "Access" => \_::$User->AdminAccess, "Image" => "home"),
        "Admin-Content" => array(
            "Name" => "CONTENTS",
            "Path" => "/admin/content/contents",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage all contents in the website",
            "Image" => "th-large",
            "Items" => array(
                array("Name" => "CONTENTS", "Path" => "/admin/content/contents", "Access" => \_::$User->AdminAccess, "Description" => "To manage website's posts and pages", "Image" => "file"),
                array("Name" => "TAGS", "Path" => "/admin/content/tags", "Access" => \_::$User->AdminAccess, "Description" => "To manage website's tags", "Image" => "tags"),
                array("Name" => "CATEGORIES", "Path" => "/admin/content/categories", "Access" => \_::$User->AdminAccess, "Description" => "To manage website's categories", "Image" => "code-fork"),
                array("Name" => "FILES", "Path" => "/admin/storage/dynamic", "Access" => \_::$User->AdminAccess, "Description" => "'Uploaded' 'files' 'management'", "Image" => "download"),
                array("Name" => "'ORGANIZED' 'STORAGE'", "Path" => "/admin/storage/static", "Access" => \_::$User->AdminAccess, "Description" => "'Organized' 'files' 'management'", "Image" => "folder"),
            )
        ),
        "Admin-User" => array(
            "Name" => "USERS",
            "Path" => "/admin/user/users",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage all interactions with the website",
            "Image" => "user",
            "Items" => array(
                array("Name" => "USERS", "Path" => "/admin/user/users", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the website's users", "Image" => "user"),
                array("Name" => "GROUPS", "Path" => "/admin/user/groups", "Access" => \_::$User->AdminAccess, "Description" => "To manage all the user groups of the website", "Image" => "user-group"),
                array("Name" => "COMMENTS", "Path" => "/admin/user/comments", "Access" => \_::$User->AdminAccess, "Description" => "To manage all received comments", "Image" => "comment"),
                array("Name" => "MESSAGES", "Path" => "/admin/user/messages", "Access" => \_::$User->AdminAccess, "Description" => "To manage all received emails and messages", "Image" => "envelope"),
                array("Name" => "SESSIONS", "Path" => "/admin/user/sessions", "Access" => \_::$User->AdminAccess, "Description" => "To manage all 'sessions'", "Image" => "clock")
            )
        ),
        "Admin-System" => array(
            "Name" => "SYSTEMS",
            "Path" => "/admin/system/information",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage the back-end website settings",
            "Image" => "cog",
            "Items" => array(
                array("Name" => "INFORMATION", "Path" => "/admin/system/information", "Access" => \_::$User->AdminAccess, "Image" => "palette"),
                array("Name" => "TRANSLATION", "Path" => "/admin/system/translation", "Access" => \_::$User->AdminAccess, "Image" => "language"),
                array("Name" => "APPEARANCE", "Path" => "/admin/system/template", "Access" => \_::$User->AdminAccess, "Image" => "eye"),
                //array("Name" => "TEMPLATE", "Path" => "/admin/system/templates", "Access" => \_::$User->AdminAccess, "Image" => "th"),
                array("Name" => "PLUGINS", "Path" => "/admin/system/plugins", "Access" => \_::$User->AdminAccess, "Image" => "puzzle-piece"),
                array("Name" => "MARKET", "Path" => "http://github.com/aseqbase", "Access" => \_::$User->AdminAccess, "Image" => "shopping-bag"),
                array("Name" => "CONFIGURATION", "Path" => "/admin/system/configuration", "Access" => \_::$User->SuperAccess, "Image" => "cog")
            )
        ),
        "User-0" => array(
            "Name" => \_::$Front->Name,
            "Path" => \_::$Front->Path,
            "Access" => \_::$User->AdminAccess,
            "Description" => "The main menu of the website",
            "Image" => "globe",
            "Items" => \_::$Front->MainMenus
        )
    );
    \_::$Front->Shortcuts = array(
        "Admin-1" => array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
        "Admin-2" => array("Name" => "CONTENTS", "Access" => \_::$User->AdminAccess, "Path" => "/admin/content/contents", "Image" => "th-large"),
        "Admin-0" => array("Name" => "HOME", "Access" => \_::$User->AdminAccess, "Path" => "/sign/dashboard", "Image" => "home"),
        "Admin-3" => array("Name" => "USERS", "Access" => \_::$User->AdminAccess, "Path" => "/admin/user/users", "Image" => "user"),
        "Admin-4" => array("Name" => "SYSTEMS", "Access" => \_::$User->AdminAccess, "Path" => "/admin/system/information", "Image" => "cog"),
    );
}

\_::$Router
    ->On()->Reset()
    ->On("~administrator")->Get(function () {
        $up = "~administrator";
        $pairs = \_::$User->GroupDataTable->SelectPairs("Id", "Access", "Access>900000000");
        if (!$pairs)
            deliverError("There is not at least one admin access group!");
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
                        MiMFa\Library\Struct::Heading1("'Your Admin Account Created Successfully' " . MiMFa\Library\Struct::Icon("print", "window.print();", ["class" => "view unptintable"])) .
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
    ->On("$|admin")->Default(fn() => view("part", ["Name" => \_::$User->InHandlerPath]))
    ->On()->Default(\_::$Router->DefaultRouteName)
    ->else()
    ->On("admin")->Reset()->Default(\_::$User->Direction, alternative: \_::$Router->DefaultRouteName);
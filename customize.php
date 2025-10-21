<?php
\_::$User->AllowSigning = true;
\_::$Config->ReportError = \_::$Config->ReportError?:E_ALL;
\_::$Config->DisplayError = \_::$Config->DisplayError?:1;
\_::$Config->DisplayStartupError = \_::$Config->DisplayStartupError?:1;
\_::$Back->DataBaseError = \_::$Back->DataBaseError?:1;
if (\_::$User->GetAccess(\_::$User->AdminAccess)) {
    \_::$Config->AdminOrigin = array_key_first(\_::$Sequences) === __DIR__.DIRECTORY_SEPARATOR?0:1;
    $name = \_::$Router->Name ?? "qb";
    if(\_::$Config->AdminOrigin===0) \_::$Router = new Router(isset($_COOKIE["BASE"]) ? $_COOKIE["BASE"] : null, array_keys(\_::$Sequences)[\_::$Config->AdminOrigin+1], array_values(\_::$Sequences)[\_::$Config->AdminOrigin+1]);
    if(\_::$Back->DataBaseAddNameToPrefix) \_::$Back->DataBasePrefix = str_replace("{$name}_", (\_::$Router->Name ?? "qb")."_", \_::$Back->DataBasePrefix);
    \_::$Info->SenderEmail = "do-not-reply@" . getDomain(\_::$Address->Root);
    \_::$Info->ReceiverEmail = "info@" . getDomain(\_::$Address->Root);
    \_::$Info->MainMenus = \_::$Info->SideMenus = array(
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
                array("Name" => "CATEGORIES", "Path" => "/admin/content/categories", "Access" => \_::$User->AdminAccess, "Description" => "To manage website's categories", "Image" => "code-fork")
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
                array("Name" => "MESSAGES", "Path" => "/admin/user/messages", "Access" => \_::$User->AdminAccess, "Description" => "To manage all received emails and messages", "Image" => "envelope")
            )
        ),
        // "Admin-Plugin" => array(
        //     "Name" => "PLUGINS",
        //     "Path" => "/admin/plugin/plugins",
        //     "Access" => \_::$User->AdminAccess,
        //     "Description" => "To manage modules and components of the website",
        //     "Image" => "puzzle-piece",
        //     "Items" => array(
        //         array("Name" => "PLUGINS", "Path" => "/admin/plugin/plugins", "Access" => \_::$User->AdminAccess, "Image" => "puzzle-piece"),
        //         array("Name" => "MARKET", "Path" => "http://github.com/aseqbase", "Access" => \_::$User->AdminAccess, "Image" => "shopping-bag")
        //     )
        // ),
        // "Admin-Front" => array(
        //     "Name" => "APPEARANCES",
        //     "Path" => "/admin/system/template",
        //     "Access" => \_::$User->AdminAccess,
        //     "Description" => "To manage the fornt-end website (template and appearances)",
        //     "Image" => "th",
        //     "Items" => array(
        //         array("Name" => "TEMPLATES", "Path" => "/admin/system/templates", "Access" => \_::$User->AdminAccess, "Image" => "eye"),
        //         array("Name" => "EDIT", "Path" => "/admin/system/template", "Access" => \_::$User->AdminAccess, "Image" => "edit")
        //     )
        // ),
        "Admin-Back" => array(
            "Name" => "SYSTEMS",
            "Path" => "/admin/system/information",
            "Access" => \_::$User->AdminAccess,
            "Description" => "To manage the back-end website settings",
            "Image" => "cog",
            "Items" => array(
                //array("Name" => "INFORMATIONS", "Path" => "/admin/system/information", "Access" => \_::$User->AdminAccess, "Image" => "info"),
                array("Name" => "TRANSLATIONS", "Path" => "/admin/system/translation", "Access" => \_::$User->AdminAccess, "Image" => "language"),
                //array("Name" => "CONFIGURATIONS", "Path" => "/admin/system/configuration", "Access" => \_::$User->AdminAccess, "Image" => "cog")
            )
        ),
        "User-0" => array(
            "Name" => \_::$Info->Name,
            "Path" => \_::$Info->Path,
            "Access" => \_::$User->AdminAccess,
            "Description" => "The main menu of the website",
            "Image" => "globe",
            "Items" => \_::$Info->MainMenus
        )
    );
    \_::$Info->Shortcuts = array(
        "Admin-1" => array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
        "Admin-2" => array("Name" => "CONTENTS", "Access" => \_::$User->AdminAccess, "Path" => "/admin/content/contents", "Image" => "th-large"),
        "Admin-0" => array("Name" => "HOME", "Access" => \_::$User->AdminAccess, "Path" => "/sign/dashboard", "Image" => "home"),
        "Admin-3" => array("Name" => "USERS", "Access" => \_::$User->AdminAccess, "Path" => "/admin/user/users", "Image" => "user"),
        "Admin-4" => array("Name" => "SYSTEMS", "Access" => \_::$User->AdminAccess, "Path" => "/admin/system/information", "Image" => "cog"),
    );
}
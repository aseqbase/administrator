<?php
\_::$Config->AllowSigning = true;
\_::$Config->ReportError = \_::$Config->ReportError?\_::$Config->ReportError:E_ALL;
\_::$Config->DisplayError = \_::$Config->DisplayError?\_::$Config->DisplayError:1;
\_::$Config->DisplayStartupError = \_::$Config->DisplayStartupError?\_::$Config->DisplayStartupError:1;
\_::$Config->DataBaseError = \_::$Config->DataBaseError?\_::$Config->DataBaseError:1;
if (auth(\_::$Config->AdminAccess)) {
    \_::$Config->AdminOrigin = array_key_first(\_::$Sequences) === __DIR__.DIRECTORY_SEPARATOR?0:1;
    $name = \_::$Aseq->Name ?? "qb";
    \_::$Aseq = new Address(isset($_COOKIE["BASE"]) ? $_COOKIE["BASE"] : null, array_keys(\_::$Sequences)[\_::$Config->AdminOrigin+1], array_values(\_::$Sequences)[\_::$Config->AdminOrigin+1]);
    if(\_::$Config->DataBaseAddNameToPrefix) \_::$Config->DataBasePrefix = str_replace("{$name}_", (\_::$Aseq->Name ?? "qb")."_", \_::$Config->DataBasePrefix);
    \_::$Info->SenderEmail = "do-not-reply@" . getDomain(\_::$Aseq->Route);
    \_::$Info->ReceiverEmail = "info@" . getDomain(\_::$Aseq->Route);
    \_::$Info->MainMenus = \_::$Info->SideMenus = array(
        "Admin-Main" => array("Name" => "DASHBOARD", "Path" => "/sign/dashboard", "Access" => \_::$Config->AdminAccess, "Image" => "home"),
        "Admin-Content" => array(
            "Name" => "CONTENTS",
            "Path" => "/admin/content/contents",
            "Access" => \_::$Config->AdminAccess,
            "Description" => "To manage all contents in the website",
            "Image" => "th-large",
            "Items" => array(
                array("Name" => "CONTENTS", "Path" => "/admin/content/contents", "Access" => \_::$Config->AdminAccess, "Description" => "To manage website's posts and pages", "Image" => "file"),
                array("Name" => "TAGS", "Path" => "/admin/content/tags", "Access" => \_::$Config->AdminAccess, "Description" => "To manage website's tags", "Image" => "tags"),
                array("Name" => "CATEGORIES", "Path" => "/admin/content/categories", "Access" => \_::$Config->AdminAccess, "Description" => "To manage website's categories", "Image" => "code-fork")
            )
        ),
        "Admin-User" => array(
            "Name" => "USERS",
            "Path" => "/admin/user/users",
            "Access" => \_::$Config->AdminAccess,
            "Description" => "To manage all interactions with the website",
            "Image" => "user",
            "Items" => array(
                array("Name" => "USERS", "Path" => "/admin/user/users", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all the website's users", "Image" => "user"),
                array("Name" => "GROUPS", "Path" => "/admin/user/groups", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all the user groups of the website", "Image" => "user-group"),
                array("Name" => "COMMENTS", "Path" => "/admin/user/comments", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all received comments", "Image" => "comment"),
                array("Name" => "MESSAGES", "Path" => "/admin/user/messages", "Access" => \_::$Config->AdminAccess, "Description" => "To manage all received emails and messages", "Image" => "envelope")
            )
        ),
        // "Admin-Plugin" => array(
        //     "Name" => "PLUGINS",
        //     "Path" => "/admin/plugin/plugins",
        //     "Access" => \_::$Config->AdminAccess,
        //     "Description" => "To manage modules and components of the website",
        //     "Image" => "puzzle-piece",
        //     "Items" => array(
        //         array("Name" => "PLUGINS", "Path" => "/admin/plugin/plugins", "Access" => \_::$Config->AdminAccess, "Image" => "puzzle-piece"),
        //         array("Name" => "MARKET", "Path" => "http://github.com/aseqbase", "Access" => \_::$Config->AdminAccess, "Image" => "shopping-bag")
        //     )
        // ),
        // "Admin-Front" => array(
        //     "Name" => "APPEARANCES",
        //     "Path" => "/admin/system/template",
        //     "Access" => \_::$Config->AdminAccess,
        //     "Description" => "To manage the fornt-end website (template and appearances)",
        //     "Image" => "th",
        //     "Items" => array(
        //         array("Name" => "TEMPLATES", "Path" => "/admin/system/templates", "Access" => \_::$Config->AdminAccess, "Image" => "eye"),
        //         array("Name" => "EDIT", "Path" => "/admin/system/template", "Access" => \_::$Config->AdminAccess, "Image" => "edit")
        //     )
        // ),
        "Admin-Back" => array(
            "Name" => "SYSTEMS",
            "Path" => "/admin/system/information",
            "Access" => \_::$Config->AdminAccess,
            "Description" => "To manage the back-end website settings",
            "Image" => "cog",
            "Items" => array(
                array("Name" => "INFORMATIONS", "Path" => "/admin/system/information", "Access" => \_::$Config->AdminAccess, "Image" => "info"),
                array("Name" => "TRANSLATIONS", "Path" => "/admin/system/translation", "Access" => \_::$Config->AdminAccess, "Image" => "language"),
                //array("Name" => "CONFIGURATIONS", "Path" => "/admin/system/configuration", "Access" => \_::$Config->AdminAccess, "Image" => "cog")
            )
        ),
        "User-0" => array(
            "Name" => \_::$Info->Name,
            "Path" => \_::$Info->Path,
            "Access" => \_::$Config->AdminAccess,
            "Description" => "The main menu of the website",
            "Image" => "globe",
            "Items" => \_::$Info->MainMenus
        )
    );
    \_::$Info->Shortcuts = array(
        "Admin-1" => array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
        "Admin-2" => array("Name" => "CONTENTS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/contents", "Image" => "th-large"),
        "Admin-0" => array("Name" => "HOME", "Access" => \_::$Config->AdminAccess, "Path" => "/sign/dashboard", "Image" => "home"),
        "Admin-3" => array("Name" => "USERS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/user/users", "Image" => "user"),
        "Admin-4" => array("Name" => "SYSTEMS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/information", "Image" => "cog"),
    );
}
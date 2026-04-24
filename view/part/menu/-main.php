<?php
$module = new (module("MainMenu"))();
$module->Title = \_::$Front->Name;
$module->Description = \_::$Front->Owner;
$module->Image = \_::$Front->LogoPath;
$module->Items = [...\_::$Front->AdminMenus, 
        "Main" => array(
            "Name" => \_::$Front->Name,
            "Path" => \_::$Front->DirectPath,
            "Access" => \_::$User->AdminAccess,
            "Description" => "The main menu of the website",
            "Image" => "globe",
            "Items" => \_::$Front->MainMenus
        )];
$module->AllowItemsImage = false;
$module->AllowSubItemsImage = true;
$module->AllowFixed = false;
pod($module, $data);
$module->Render();
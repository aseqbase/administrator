<?php
$module = new (module("MainMenu"))();
$module->Title = \_::$Front->Name;
$module->Description = \_::$Front->Owner;
$module->Image = \_::$Front->LogoPath;
$module->Items = \_::$Front->MainMenus;
$module->AllowItemsImage = false;
$module->AllowSubItemsImage = true;
$module->AllowFixed = false;
pod($module, $data);
$module->Render();
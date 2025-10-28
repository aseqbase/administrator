<?php
$module = new (module("MainMenu"))();
$module->Title = \_::$Info->Name;
$module->Description = \_::$Info->Owner;
$module->Image = \_::$Info->LogoPath;
$module->Items = \_::$Info->MainMenus;
$module->AllowItemsImage = false;
$module->AllowSubItemsImage = true;
$module->AllowFixed = false;
pod($module, $data);
$module->Render();
<?php
$module = new (module("BarMenu"))();
$module->AllowChangeColor = true;
$module->AllowAnimate = 
$module->AllowMiddle = false;
$module->Items = \_::$Front->AdminShortcuts;
pod($module, $data);
$module->Render();
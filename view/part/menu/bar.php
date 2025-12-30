<?php
$module = new (module("BarMenu"))();
$module->AllowChangeColor = true;
$module->AllowAnimate = 
$module->AllowMiddle = false;
$module->Items = \_::$Front->Shortcuts;
pod($module, $data);
$module->Render();
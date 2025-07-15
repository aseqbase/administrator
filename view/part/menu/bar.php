<?php
$module = new (module("BarMenu"))();
$module->AllowChangeColor = true;
$module->AllowAnimate = 
$module->AllowMiddle = false;
$module->Items = \_::$Info->Shortcuts;
swap($module, $data);
$module->Render();
<?php
ACCESS(\_::$CONFIG->AdminAccess);
MODULE("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Configuration";
$module->Draw();
echo \MiMFa\Library\HTML::Page(PART("system/configuration", print:false));
?>
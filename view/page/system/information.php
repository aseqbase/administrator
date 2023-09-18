<?php
ACCESS(\_::$CONFIG->AdminAccess);
MODULE("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Information";
$module->Draw();
echo \MiMFa\Library\HTML::Page(PART("system/information", print:false));
?>
<?php
ACCESS(\_::$CONFIG->AdminAccess);
MODULE("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Templates";
$module->Draw();
echo \MiMFa\Library\HTML::Page(PART("table/templates", print:false));
?>
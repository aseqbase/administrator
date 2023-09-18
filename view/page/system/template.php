<?php
ACCESS(\_::$CONFIG->AdminAccess);
MODULE("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Template";
$module->Draw();
echo \MiMFa\Library\HTML::Page(PART("system/template", print:false));
?>
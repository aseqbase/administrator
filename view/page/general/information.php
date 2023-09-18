<?php
ACCESS(\_::$CONFIG->AdminAccess);
MODULE("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Informations";
$module->Draw();
echo \MiMFa\Library\HTML::Page(PART("general/information", print:false));
?>
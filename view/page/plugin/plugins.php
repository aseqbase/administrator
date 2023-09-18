<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(!RECEIVE()){
    MODULE("PrePage");
    $module = new MiMFa\Module\PrePage();
    $module->Title = "Plugins Management";
    $module->Draw();
}
echo \MiMFa\Library\HTML::Page(PART("table/plugins", print:false));
?>
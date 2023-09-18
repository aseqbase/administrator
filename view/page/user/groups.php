<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(!RECEIVE()){
    MODULE("PrePage");
    $module = new MiMFa\Module\PrePage();
    $module->Title = "User Groups Management";
    $module->Draw();
}
echo \MiMFa\Library\HTML::Page(PART("table/usergroups", print:false));
?>
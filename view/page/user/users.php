<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(!RECEIVE()){
    MODULE("PrePage");
    $module = new MiMFa\Module\PrePage();
    $module->Title = "Users Management";
    $module->Draw();
}
echo \MiMFa\Library\HTML::Page(PART("table/users", print:false));
?>
<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(!RECEIVE()){
    MODULE("PrePage");
    $module = new MiMFa\Module\PrePage();
    $module->Image = "/asset/symbol/directory.png";
    $module->Title = "Tags Management";
    $module->Draw();
}
echo \MiMFa\Library\HTML::Page(PART("table/tags", print:false));
?>
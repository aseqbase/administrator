<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(!RECEIVE()){
    MODULE("PrePage");
    $module = new MiMFa\Module\PrePage();
    $module->Image = "/asset/symbol/document.png";
    $module->Title = "Posts Management";
    $module->Draw();
}
echo \MiMFa\Library\HTML::Page(PART("table/contents", print:false));
?>
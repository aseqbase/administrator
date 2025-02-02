<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(!RECEIVE()){
    MODULE("PrePage");
    $module = new MiMFa\Module\PrePage();
    $module->Image = "/asset/symbol/chat.png";
    $module->Title = "Comments Management";
    $module->Draw();
}
echo \MiMFa\Library\HTML::Page(PART("table/comments", print:false));
?>
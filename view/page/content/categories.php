<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(!RECEIVE()){
    MODULE("PrePage");
    $module = new MiMFa\Module\PrePage();
    $module->Image = "/asset/symbol/category.png";
    $module->Title = "Categories Management";
    $module->Draw();
}
echo \MiMFa\Library\HTML::Page(PART("table/categories", print:false));
?>
<?php
ACCESS(\_::$CONFIG->SuperAccess);
LIBRARY("Reflect");
if(RECEIVE(null,"POST")) echo \MiMFa\Library\Reflect::HandleForm(\_::$CONFIG);
else {
    $form = \MiMFa\Library\Reflect::GetForm(\_::$CONFIG);
    $form->Title = "Edit Configuration";
    $form->Id = "EditConfiguration";
    $form->SubmitLabel = null;//Remove to set the form editable
    $form->Draw();
}
?>
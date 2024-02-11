<?php
ACCESS(\_::$CONFIG->AdminAccess);
LIBRARY("Reflect");
if(RECEIVE(null,"POST")) echo \MiMFa\Library\Reflect::HandleForm(\_::$TEMPLATE);
else {
    $form = \MiMFa\Library\Reflect::GetForm(\_::$TEMPLATE);
    $form->Title = "Edit Template";
    $form->Id = "EditTemplate";
    $form->SubmitLabel = null;//Remove to set the form editable
    $form->Draw();
}
?>
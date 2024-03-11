<?php
ACCESS(\_::$CONFIG->AdminAccess);
if(RECEIVE("restore","GET")) echo \MiMFa\Library\Revise::Restore(\_::$TEMPLATE)
    ?\MiMFa\Library\HTML::Success("Data Restored Successfully!")
    :\MiMFa\Library\HTML::Warning("Data is restored!");
if(RECEIVE(null,"POST")) echo \MiMFa\Library\Revise::HandleForm(\_::$TEMPLATE);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$TEMPLATE);
    $form->Title = "Edit Template";
    $form->Id = "EditTemplate";
    $form->Buttons = \MiMFa\Library\HTML::Button("Restore",\_::$PATH."?restore=true");
    $form->Draw();
}
?>
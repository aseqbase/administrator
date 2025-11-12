<?php
auth(\_::$User->AdminAccess);
if(receiveGet("restore")) echo \MiMFa\Library\Revise::Restore(\_::$Front)
    ?\MiMFa\Library\Struct::Success("Data Restored Successfully!")
    :\MiMFa\Library\Struct::Warning("Data is restored!");
if(receivePost(null)) echo \MiMFa\Library\Revise::HandleForm(\_::$Front);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Front);
    $form->Title = "Edit Template";
    $form->Id = "EditTemplate";
    $form->Buttons = \MiMFa\Library\Struct::Button("Restore",\_::$Address->Path."?restore=true");
    $form->Render();
}
?>
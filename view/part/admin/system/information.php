<?php
auth(\_::$User->SuperAccess);
if(receiveGet("restore")) echo \MiMFa\Library\Revise::Restore(\_::$Front)
    ?\MiMFa\Library\Struct::Success("Data Restored Successfully!")
    :\MiMFa\Library\Struct::Warning("Data is restored!");
\_::$User->Active = false;
if(receivePost(null)) echo \MiMFa\Library\Revise::HandleForm(\_::$Front);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Front);
    $form->Title = "Edit Information";
    $form->Id = "EditInformation";
    $form->Buttons = \MiMFa\Library\Struct::Button("Restore",\_::$User->Path."?restore=true");
    $form->Render();
}
\_::$User->Active = true;
?>
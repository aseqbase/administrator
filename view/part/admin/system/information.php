<?php
auth(\_::$User->SuperAccess);
if(receiveGet("restore")) echo \MiMFa\Library\Revise::Restore(\_::$Info)
    ?\MiMFa\Library\Struct::Success("Data Restored Successfully!")
    :\MiMFa\Library\Struct::Warning("Data is restored!");
\_::$User->Active = false;
if(receivePost(null)) echo \MiMFa\Library\Revise::HandleForm(\_::$Info);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Info);
    $form->Title = "Edit Information";
    $form->Id = "EditInformation";
    $form->Buttons = \MiMFa\Library\Struct::Button("Restore",\_::$User->Path."?restore=true");
    $form->Render();
}
\_::$User->Active = true;
?>
<?php
inspect(\_::$Config->SuperAccess);
if(receiveGet("restore")) echo \MiMFa\Library\Revise::Restore(\_::$Info)
    ?\MiMFa\Library\Html::Success("Data Restored Successfully!")
    :\MiMFa\Library\Html::Warning("Data is restored!");
\User::$Active = false;
if(receivePost(null)) echo \MiMFa\Library\Revise::HandleForm(\_::$Info);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Info);
    $form->Title = "Edit Information";
    $form->Id = "EditInformation";
    $form->Buttons = \MiMFa\Library\Html::Button("Restore",\_::$Path."?restore=true");
    $form->Render();
}
\User::$Active = true;
?>
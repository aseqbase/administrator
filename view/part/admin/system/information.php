<?php
inspect(\_::$Config->SuperAccess);
if(\Req::Receive("restore","GET")) echo \MiMFa\Library\Revise::Restore(\_::$Info)
    ?\MiMFa\Library\Html::Success("Data Restored Successfully!")
    :\MiMFa\Library\Html::Warning("Data is restored!");
\MiMFa\Library\User::$Active = false;
if(\Req::Receive(null,"POST")) echo \MiMFa\Library\Revise::HandleForm(\_::$Info);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Info);
    $form->Title = "Edit Information";
    $form->Id = "EditInformation";
    $form->Buttons = \MiMFa\Library\Html::Button("Restore",\Req::$Path."?restore=true");
    $form->Render();
}
\MiMFa\Library\User::$Active = true;
?>
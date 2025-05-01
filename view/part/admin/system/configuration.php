<?php
inspect(\_::$Config->SuperAccess);
if(\Req::Receive("restore","GET")) echo \MiMFa\Library\Revise::Restore(\_::$Front)
    ?\MiMFa\Library\Html::Success("Data Restored Successfully!")
    :\MiMFa\Library\Html::Warning("Data is restored!");
if(\Req::Receive(null,"POST")) echo \MiMFa\Library\Revise::HandleForm(\_::$Config);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Config);
    $form->Title = "Edit Configuration";
    $form->Id = "EditConfiguration";
    $form->Buttons = \MiMFa\Library\Html::Button("Restore",\Req::$Path."?restore=true");
    $form->Render();
}
?>
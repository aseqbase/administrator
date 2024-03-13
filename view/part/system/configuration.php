<?php
ACCESS(\_::$CONFIG->SuperAccess);
if(RECEIVE("restore","GET")) echo \MiMFa\Library\Revise::Restore(\_::$TEMPLATE)
    ?\MiMFa\Library\HTML::Success("Data Restored Successfully!")
    :\MiMFa\Library\HTML::Warning("Data is restored!");
if(RECEIVE(null,"POST")) echo \MiMFa\Library\Revise::HandleForm(\_::$CONFIG);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$CONFIG);
    $form->Title = "Edit Configuration";
    $form->Id = "EditConfiguration";
    $form->Buttons = \MiMFa\Library\HTML::Button("Restore",\_::$PATH."?restore=true");
    $form->Draw();
}
?>
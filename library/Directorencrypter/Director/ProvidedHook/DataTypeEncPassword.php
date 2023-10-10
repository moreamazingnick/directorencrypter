<?php

namespace Icinga\Module\Directorencrypter\Director\ProvidedHook;

use Icinga\Application\Icinga;
use Icinga\Module\Director\Hook\DataTypeHook;
use Icinga\Module\Director\Web\Form\QuickForm;

class DataTypeEncPassword extends DataTypeHook
{
    public function getFormElement($name, QuickForm $form)
    {
        $module = Icinga::app()
            ->getModuleManager()
            ->loadModule('directorencrypter')
            ->getModule('directorencrypter');

        $form->addPrefixPathsForModule($module);
        $element = $form->createElement('storedEncPassword', $name);
        //&& (preg_match('/ENC\(.+?\)/', $value) === false
/*
        $form->callOnRequest(function (DirectorForm $form) use ($name) {
            $value = $form->getElement($name)->getValue();
            Logger::error(sprintf("before => field:%s, value:%s",$name,$value));
            if($value !== "__UNCHANGED_VALUE__" ){
                $value = sprintf("PREENC(%s)", $value);
                $form->getElement($name)->setValue($value);
            }
            Logger::error(sprintf("after => field:%s, value:%s",$name,$value));


        });*/
        return $element;
    }

    public static function addSettingsFormFields(QuickForm $form)
    {
        return $form;
    }
}

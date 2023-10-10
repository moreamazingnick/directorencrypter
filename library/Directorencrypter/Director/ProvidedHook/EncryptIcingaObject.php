<?php

namespace Icinga\Module\Directorencrypter\Director\ProvidedHook;


use Icinga\Module\Director\Hook\BeforeStoreIcingaObjectHook;
use Icinga\Module\Directorencrypter\Encrypter\EncUtils;

class EncryptIcingaObject extends BeforeStoreIcingaObjectHook
{
    public static function manipulateIcingaObject($object)
    {
        parent::manipulateIcingaObject($object);
        EncUtils::encryptIcingaObject($object);
    }
}
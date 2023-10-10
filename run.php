<?php
use Icinga\Module\Directorencrypter\Director\ProvidedHook\DataTypeEncPassword;
use Icinga\Module\Directorencrypter\Director\ProvidedHook\EncryptIcingaObject;
use Icinga\Module\Directorencrypter\Director\ProvidedHook\DecryptIcingaConfigFiles;

$this->provideHook('director/DataType', DataTypeEncPassword::class);
$this->provideHook('director/BeforeStoreIcingaObject', EncryptIcingaObject::class);
$this->provideHook('director/Deployment', DecryptIcingaConfigFiles::class);
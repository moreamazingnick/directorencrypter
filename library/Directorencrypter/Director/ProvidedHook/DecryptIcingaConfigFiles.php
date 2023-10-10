<?php

namespace Icinga\Module\Directorencrypter\Director\ProvidedHook;

use Icinga\Module\Director\Daemon\Logger;
use Icinga\Module\Director\Hook\DeploymentHook;
use Icinga\Module\Director\Objects\DirectorDeploymentLog;
use Icinga\Module\Directorencrypter\Encrypter\EncUtils;

class DecryptIcingaConfigFiles extends DeploymentHook
{

    public function beforeDump($files)
    {
        $files = parent::beforeDump($files);

        return EncUtils::decryptIcingaConfig($files);

    }
}
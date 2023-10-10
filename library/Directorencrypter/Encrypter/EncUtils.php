<?php

namespace Icinga\Module\Directorencrypter\Encrypter;

use Icinga\Application\Config;
use Icinga\Module\Director\CustomVariable\CustomVariableDictionary;
use Icinga\Module\Director\CustomVariable\CustomVariables;
use Icinga\Module\Director\CustomVariable\CustomVariableString;
use Icinga\Module\Director\Objects\IcingaObject;

class EncUtils
{

    public static function  encrypt_decrypt($action, $string)
    {
        $encrypt_method = "AES-256-CBC";

        $encryption_iv = Config::module('directorencrypter',"encryption")->get('encryption', 'initializationvector');
        $encryption_key = Config::module('directorencrypter',"encryption")->get('encryption', 'key');
        $encryption_salt = Config::module('directorencrypter',"encryption")->get('encryption', 'salt');
        if($encryption_iv == null){
            $ivlen = openssl_cipher_iv_length($encrypt_method);
            $encryption_iv = base64_encode(openssl_random_pseudo_bytes($ivlen));
            Config::module('directorencrypter',"encryption")->setSection("encryption",['initializationvector'=>$encryption_iv, 'key'=>$encryption_key, 'salt'=>$encryption_salt ])->saveIni();
        }

        if($encryption_key == null){
            $encryption_key = base64_encode(openssl_random_pseudo_bytes(20));
            Config::module('directorencrypter',"encryption")->setSection("encryption",['initializationvector'=>$encryption_iv, 'key'=>$encryption_key, 'salt'=>$encryption_salt ])->saveIni();
        }

        if($encryption_salt == null){
            $encryption_salt = base64_encode(openssl_random_pseudo_bytes(20));
            Config::module('directorencrypter',"encryption")->setSection("encryption",['initializationvector'=>$encryption_iv, 'key'=>$encryption_key, 'salt'=>$encryption_salt ])->saveIni();
        }
        $output = false;

        $salt = sprintf("SALT:%s:",$encryption_salt);
        // hash
        $key = hash('sha256', $encryption_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $encryption_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt(sprintf("%s%s",$salt,$string), $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            $output = str_replace($salt,"",$output);
        }
        return $output;
    }

    public static function decryptIcingaConfig(array $files){
        foreach ($files as $file=>$content){
            $files[$file]  = preg_replace_callback(
                '/ENC\((.*?)\)/',
                function ($matches) {
                    #print_r($matches);
                    return EncUtils::encrypt_decrypt("decrypt",$matches[1]);
                },
                $content


            );
        }
        return $files;
    }

    public static function encryptIcingaObject(IcingaObject $object){

        if ($object->supportsCustomVars()) {
            $vars = $object->vars();

            /** @var CustomVariables $vars */
            foreach ($vars as $key => $var) {
                if($var instanceof CustomVariableString){
                    /** @var CustomVariableString $var */
                    $var = $var->getValue();
                    $var = preg_replace_callback(
                        '/PREENC\((.*?)\)/',
                        function ($matches) {
                            #print_r($matches);
                            return sprintf('ENC(%s)',EncUtils::encrypt_decrypt("encrypt",$matches[1]));
                        },
                        $var
                    );
                    $vars->set($key,$var );

                }
                if($var instanceof CustomVariableDictionary){
                    $json = $var->getDbValue();
                    $encjson = preg_replace_callback(
                        '/PREENC\((.*?)\)/',
                        function ($matches) {
                            #print_r($matches);
                            return sprintf('ENC(%s)',EncUtils::encrypt_decrypt("encrypt",$matches[1]));
                        },
                        $json
                    );
                    $customVariableDictionary = json_decode($encjson);
                    $vars->set($key,$customVariableDictionary );
                }


            }

        }
        return $object;
    }

}

<?php
class Globals {
    const app = 'PORTAL';
    const version= '1.0';
    const cssversion= "1.0";
    // KB
    public static $jsonrpcURL= "";
    public static $token= "";
    public static $postmantoken= "";
    private static $config="";
    // WAS LDAP
    public static $adServer="";
    public static $ldapport="";
    public static $userconn="";
    public static $pwconn="";
    
    public static function ConfiguracionIni(){     
        if (file_exists('../../ini/config.ini')) {
            self::$config = parse_ini_file('../../ini/config.ini',true); 
        } 
        else if (file_exists('../ini/config.ini')) {
            self::$config = parse_ini_file('../ini/config.ini',true); 
        }
        else if (file_exists('../../../ini/config.ini')) {
            self::$config = parse_ini_file('../../../ini/config.ini',true); 
        }   
        //
        self::$jsonrpcURL= self::$config[self::app]['jsonrpcURL'];
        self::$token= self::$config[self::app]['token'];
        self::$postmantoken= self::$config[self::app]['postmantoken'];
    }  

    public static function ConfiguracionLdap(){     
        error_reporting(1);
        ini_set('error_reporting', 1);
        if (file_exists('../../ini/config.ini')) {
            self::$config = parse_ini_file('../../ini/config.ini',true); 
        } 
        else if (file_exists('../ini/config.ini')) {
            self::$config = parse_ini_file('../ini/config.ini',true); 
        }
        else if (file_exists('../../../ini/config.ini')) {
            self::$config = parse_ini_file('../../../ini/config.ini',true); 
        }   
        //
        self::$adServer= self::$config['WAS']['adserver'];
        self::$ldapport= self::$config['WAS']['port'];
        self::$userconn= self::$config['WAS']['userconn'];
        self::$pwconn= self::$config['WAS']['pwconn'];
    }  
}
?>
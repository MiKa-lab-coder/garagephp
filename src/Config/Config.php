<?php
namespace App\Config;
use Dotenv\Dotenv;

class Config {

//    //class config manuelle, cette class serta acharger le fichier .env, a le lire et separer
//    //et nettoyer les données
//    private static array $config=[];
//
//    private static bool $loaded=false;
//
//    public static function load(): void {
//        if (self::$loaded) return;
//
//            $envFile=__DIR__.'/../../.env';
//            if (!file_exists($envFile)) {
//                throw new \Exception("fichier .env manquant");
//        }
//            $lines=file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//            foreach ($lines as $line) {
//                if (strpos(trim($line), '#') === 0) continue;
//
//                list($key, $value)=explode('=', $line,2);
//                $key=trim($key);
//                $value=trim(trim($value),'"\'');
//
//                self::$config[$key]=$value;
//                $_ENV[$key]=$value;
//                putenv("$key=$value");
//            }
//            self::validateConfig();
//            self::$loaded=true;
//    }
//    public static function get(string $key, $default=null) {
//        if(!self::$loaded){
//            self::load();
//        }
//        return self::$config[$key] ?? $default;
//    }
////    private static function validateConfig(): void {
////
////        $required=['DB_HOST','DB_NAME','DB_USER','APP_KEY'];
////        $missing=array_filter($required,fn($key)=> empty(self::$config[$key]));
////        if(!empty($missing)){
////            throw new \Exception("variable environment manquante :", implode(', ',$missing));
////        }
////    }
////    public static function isDebug(): bool {
////        return self::get('APP_DEBUG',false) === 'true';
////    }

/*
 * @param string $path => chemin vers le dossier .env
 */
    public static function load($path = __DIR__."../"): void {
        //on verifie si le fichier .env exist avant de tenter de le charger
        if (file_exists($path.'.env')) {
            $dotenv = Dotenv::createImmutable($path);
            $dotenv->load();
        }
    }
    /*
     * @param string $key => le nom de la variable
     * @param mixed $default => valeur par defaut a retourne si la varible n'existe pas
     * @return mixed la valeur de la variable ou la valeur par defaut
     */
    public static function get(string $key, $default=null) {
        return $_ENV[$key] ?? $default;
    }
}
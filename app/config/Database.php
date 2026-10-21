<?php
namespace MyBox\Config;
use PDO;
final class Database {
 private static ?PDO $db=null;
 public static function connection():PDO {
  if(self::$db) return self::$db;
  $host=getenv('DB_HOST')?:'127.0.0.1'; $name=getenv('DB_NAME')?:'mybox';
  $user=getenv('DB_USER'); if($user===false || $user==='') throw new \RuntimeException('Configura DB_USER en el servidor.');
  $pass=getenv('DB_PASS');
  return self::$db=new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4",$user,$pass===false?'':$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
 }
}

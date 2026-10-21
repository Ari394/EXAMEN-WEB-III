<?php
namespace MyBox\Models;
use PDO;
final class Usuario {
 public function __construct(private PDO $db){}
 public function findByCredentials(string $username,string $password):?array {
  $q=$this->db->prepare('SELECT id,usuario,nombre,correo,password FROM usuarios WHERE usuario=?');$q->execute([$username]);$u=$q->fetch();
  if(!$u || !password_verify($password,(string)$u['password']))return null;
  unset($u['password']);return $u;
 }
 public function create(string $username,string $password,string $name,string $email):int {
  $q=$this->db->prepare('INSERT INTO usuarios (usuario,password,nombre,correo) VALUES (?,?,?,?)');
  $q->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name,$email]);return (int)$this->db->lastInsertId();
 }
}

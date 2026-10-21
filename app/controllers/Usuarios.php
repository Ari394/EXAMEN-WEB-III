<?php
namespace MyBox\Controllers;
use MyBox\Config\Database;
use MyBox\Models\Usuario;
final class Usuarios {
 public function authenticate(string $username,string $password):?array {
  if(trim($username)==='' || $password==='')return null;
  return (new Usuario(Database::connection()))->findByCredentials(trim($username),$password);
 }
 public function register(string $username,string $password,string $name,string $email):int {
  $username=trim($username);$name=trim($name);$email=trim($email);
  if($username==='' || $name==='' || $email==='' || $password==='')throw new \InvalidArgumentException('Todos los campos son obligatorios.');
  if(mb_strlen($username)>50 || mb_strlen($name)>100 || strlen($email)>150 || !filter_var($email,FILTER_VALIDATE_EMAIL))throw new \InvalidArgumentException('Datos de registro inválidos.');
  if(strlen($password)<8)throw new \InvalidArgumentException('La contraseña debe tener al menos 8 caracteres.');
  return (new Usuario(Database::connection()))->create($username,$password,$name,$email);
 }
}

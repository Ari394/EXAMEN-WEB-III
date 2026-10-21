<?php
namespace MyBox\Controllers;
final class AuthWeb {
 public function login():void {$error='';require dirname(__DIR__).'/views/usuarios/login.php';}
 public function registro():void {$error='';require dirname(__DIR__).'/views/usuarios/registro.php';}
 public function authenticate():void {
  $user=(new Usuarios())->authenticate((string)($_POST['usuario']??''),(string)($_POST['password']??''));
  if(!$user){http_response_code(401);$error='Usuario o contraseña incorrectos.';require dirname(__DIR__).'/views/usuarios/login.php';return;}
  session_regenerate_id(true);$_SESSION['usuario_id']=(int)$user['id'];$_SESSION['usuario']=$user['usuario'];$_SESSION['nombre']=$user['nombre'];
  header('Location: index.php?action=carpetas');exit;
 }
 public function register():void {
  try{(new Usuarios())->register((string)($_POST['usuario']??''),(string)($_POST['password']??''),(string)($_POST['nombre']??''),(string)($_POST['correo']??''));header('Location: index.php?action=login');exit;}
  catch(\InvalidArgumentException $e){http_response_code(422);$error=$e->getMessage();}
  catch(\PDOException $e){error_log($e->getMessage());http_response_code(400);$error='No se pudo registrar; revisa usuario o correo duplicado.';}
  require dirname(__DIR__).'/views/usuarios/registro.php';
 }
 public function logout():void {$_SESSION=[];session_regenerate_id(true);header('Location: index.php?action=login');exit;}
}

<?php
namespace MyBox\Controllers;
use MyBox\Config\Database;
use MyBox\Models\CarpetaCompartida;
final class Compartir {
 private function model():CarpetaCompartida{return new CarpetaCompartida(Database::connection());}
 public function share(int $folderId,int $ownerId,string $recipient):void {
  if(trim($recipient)==='' || mb_strlen($recipient)>50)throw new \InvalidArgumentException('Selecciona un usuario válido.');
  $this->model()->share($folderId,$ownerId,$recipient);
 }
 public function unshare(int $folderId,int $ownerId,int $recipientId):bool{return $this->model()->unshare($folderId,$ownerId,$recipientId);}
 public function availableUsers(int $folderId,int $ownerId):array{return $this->model()->availableUsers($folderId,$ownerId);}
 public function recipients(int $folderId,int $ownerId):array{return $this->model()->recipients($folderId,$ownerId);}
}

<?php
namespace MyBox\Controllers;
use MyBox\Config\Database;
use MyBox\Models\CarpetaCompartida;
final class Compartir {
 public function share(int $folderId,int $ownerId,string $recipient):void {
  if(trim($recipient)==='' || mb_strlen($recipient)>50)throw new \InvalidArgumentException('Destinatario inválido.');
  (new CarpetaCompartida(Database::connection()))->share($folderId,$ownerId,$recipient);
 }
}

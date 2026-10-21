<?php
namespace MyBox\Models;
use PDO;
final class CarpetaCompartida {
 public function __construct(private PDO $db){}
 public function share(int $folderId,int $ownerId,string $recipient):void {
  $q=$this->db->prepare('SELECT 1 FROM carpetas WHERE id=? AND usuario_id=?');$q->execute([$folderId,$ownerId]);if(!$q->fetchColumn())throw new \RuntimeException('La carpeta no te pertenece.');
  $q=$this->db->prepare('SELECT id FROM usuarios WHERE usuario=?');$q->execute([trim($recipient)]);$recipientId=$q->fetchColumn();
  if(!$recipientId || (int)$recipientId===$ownerId)throw new \RuntimeException('Usuario destinatario inválido.');
  $q=$this->db->prepare('INSERT INTO carpetas_compartidas(carpeta_id,usuario_id) VALUES (?,?) ON DUPLICATE KEY UPDATE usuario_id=VALUES(usuario_id)');$q->execute([$folderId,$recipientId]);
 }
}

<?php
namespace MyBox\Models;
use PDO;
final class CarpetaCompartida {
 public function __construct(private PDO $db){}
 public function share(int $folderId,int $ownerId,string $recipient):void {
  $this->assertOwner($folderId,$ownerId);
  $q=$this->db->prepare('SELECT id FROM usuarios WHERE usuario=?');$q->execute([trim($recipient)]);$recipientId=$q->fetchColumn();
  if(!$recipientId || (int)$recipientId===$ownerId)throw new \RuntimeException('Usuario destinatario inválido.');
  $q=$this->db->prepare('INSERT INTO carpetas_compartidas(carpeta_id,usuario_id) VALUES (?,?) ON DUPLICATE KEY UPDATE usuario_id=VALUES(usuario_id)');$q->execute([$folderId,$recipientId]);
 }
 public function unshare(int $folderId,int $ownerId,int $recipientId):bool {
  $this->assertOwner($folderId,$ownerId);
  $q=$this->db->prepare('DELETE FROM carpetas_compartidas WHERE carpeta_id=? AND usuario_id=?');$q->execute([$folderId,$recipientId]);return $q->rowCount()>0;
 }
 // Usuarios registrados a los que todavía se puede compartir la carpeta.
 public function availableUsers(int $folderId,int $ownerId):array {
  $this->assertOwner($folderId,$ownerId);
  $q=$this->db->prepare('SELECT u.id,u.usuario,u.nombre FROM usuarios u WHERE u.id<>? AND u.usuario IS NOT NULL AND NOT EXISTS (SELECT 1 FROM carpetas_compartidas cc WHERE cc.carpeta_id=? AND cc.usuario_id=u.id) ORDER BY u.usuario');
  $q->execute([$ownerId,$folderId]);return $q->fetchAll();
 }
 public function recipients(int $folderId,int $ownerId):array {
  $this->assertOwner($folderId,$ownerId);
  $q=$this->db->prepare('SELECT u.id,u.usuario,u.nombre,cc.fecha_compartido FROM carpetas_compartidas cc JOIN usuarios u ON u.id=cc.usuario_id WHERE cc.carpeta_id=? ORDER BY u.usuario');
  $q->execute([$folderId]);return $q->fetchAll();
 }
 private function assertOwner(int $folderId,int $ownerId):void {
  $q=$this->db->prepare('SELECT 1 FROM carpetas WHERE id=? AND usuario_id=?');$q->execute([$folderId,$ownerId]);if(!$q->fetchColumn())throw new \RuntimeException('La carpeta no te pertenece.',403);
 }
}

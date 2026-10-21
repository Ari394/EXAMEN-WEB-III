<?php
namespace MyBox\Models;
use PDO;
final class Carpeta {
 public function __construct(private PDO $db){}
 public function owns(int $id,int $userId):bool {
  $q=$this->db->prepare('SELECT 1 FROM carpetas WHERE id=? AND usuario_id=?');$q->execute([$id,$userId]);return (bool)$q->fetchColumn();
 }
 public function canRead(int $id,int $userId):bool {
  $q=$this->db->prepare('WITH RECURSIVE anc AS (SELECT id,carpeta_padre_id,usuario_id FROM carpetas WHERE id=? UNION ALL SELECT p.id,p.carpeta_padre_id,p.usuario_id FROM carpetas p JOIN anc a ON a.carpeta_padre_id=p.id) SELECT 1 FROM anc WHERE usuario_id=? OR EXISTS (SELECT 1 FROM carpetas_compartidas cc WHERE cc.carpeta_id=anc.id AND cc.usuario_id=?) LIMIT 1');
  $q->execute([$id,$userId,$userId]);return (bool)$q->fetchColumn();
 }
 public function listForUser(int $userId,?int $parentId=null):array {
  if($parentId!==null && !$this->canRead($parentId,$userId))throw new \RuntimeException('Sin permiso para ver esta carpeta.');
  $sql='SELECT c.id,c.nombre,c.usuario_id,c.carpeta_padre_id,c.fecha_creacion,u.usuario AS propietario FROM carpetas c JOIN usuarios u ON u.id=c.usuario_id WHERE ';
  if($parentId===null){$sql.='c.usuario_id=? AND c.carpeta_padre_id IS NULL';$args=[$userId];}
  else{$sql.='c.carpeta_padre_id=?';$args=[$parentId];}
  $q=$this->db->prepare($sql.' ORDER BY c.nombre');$q->execute($args);return $q->fetchAll();
 }
 public function create(int $userId,string $name,?int $parentId):int {
  $name=trim($name);if($name==='' || mb_strlen($name)>100)throw new \InvalidArgumentException('Nombre de carpeta inválido.');
  if($parentId!==null && !$this->owns($parentId,$userId))throw new \RuntimeException('Solo puedes crear subcarpetas dentro de tus carpetas.');
  $q=$this->db->prepare('INSERT INTO carpetas(nombre,usuario_id,carpeta_padre_id) VALUES (?,?,?)');$q->execute([$name,$userId,$parentId]);return (int)$this->db->lastInsertId();
 }
 public function deleteForUser(int $id,int $userId):bool {
  $q=$this->db->prepare('DELETE FROM carpetas WHERE id=? AND usuario_id=?');$q->execute([$id,$userId]);return $q->rowCount()>0;
 }
 public function sharedRoots(int $userId):array {
  $q=$this->db->prepare('SELECT c.id,c.nombre,c.fecha_creacion,u.usuario AS propietario FROM carpetas_compartidas cc JOIN carpetas c ON c.id=cc.carpeta_id JOIN usuarios u ON u.id=c.usuario_id WHERE cc.usuario_id=? ORDER BY c.nombre');$q->execute([$userId]);return $q->fetchAll();
 }
}

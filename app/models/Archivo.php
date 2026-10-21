<?php
namespace MyBox\Models;
use PDO;
final class Archivo {
 private const LIMIT=20971520;
 public function __construct(private PDO $db){}
 public function listForUser(int $userId,?int $folderId=null):array {
  $folder=new Carpeta($this->db);
  if($folderId!==null && !$folder->canRead($folderId,$userId))throw new \RuntimeException('Sin permiso para ver archivos.');
  $sql='SELECT id,nombre,comentario,tipo,tamano,fecha_subida,usuario_id,carpeta_id FROM archivos WHERE ';
  if($folderId===null){$sql.='usuario_id=? AND carpeta_id IS NULL';$args=[$userId];}
  else{$sql.='carpeta_id=?';$args=[$folderId];}
  $q=$this->db->prepare($sql.' ORDER BY fecha_subida DESC');$q->execute($args);return $q->fetchAll();
 }
 public function upload(int $userId,?int $folderId,array $file,string $comment=''):int {
  if($folderId!==null && !(new Carpeta($this->db))->owns($folderId,$userId))throw new \RuntimeException('No puedes subir archivos a esa carpeta.');
  if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)throw new \RuntimeException('Error al recibir el archivo.');
  $path=$file['tmp_name']??'';$size=(int)($file['size']??0);
  if($size<1 || $size>self::LIMIT || !is_uploaded_file($path))throw new \RuntimeException('Archivo inválido o supera 20 MiB.');
  if(mb_strlen($comment)>50)throw new \InvalidArgumentException('Comentario máximo 50 caracteres.');
  $name=basename(str_replace('\\','/',(string)($file['name']??'')));
  if($name==='' || mb_strlen($name)>255)throw new \InvalidArgumentException('Nombre de archivo inválido.');
  $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($path)?:'application/octet-stream';
  $content=file_get_contents($path);if($content===false)throw new \RuntimeException('No se pudo leer el archivo.');
  $q=$this->db->prepare('INSERT INTO archivos(nombre,comentario,tipo,tamano,contenido,usuario_id,carpeta_id) VALUES (?,?,?,?,?,?,?)');
  $q->bindValue(1,$name);$q->bindValue(2,$comment);$q->bindValue(3,$mime);$q->bindValue(4,$size,PDO::PARAM_INT);$q->bindValue(5,$content,PDO::PARAM_LOB);$q->bindValue(6,$userId,PDO::PARAM_INT);$q->bindValue(7,$folderId,$folderId===null?PDO::PARAM_NULL:PDO::PARAM_INT);$q->execute();return (int)$this->db->lastInsertId();
 }
 public function findById(int $fileId,int $userId):?array {
  $q=$this->db->prepare('SELECT id,nombre,tipo,tamano,contenido,usuario_id,carpeta_id FROM archivos WHERE id=?');$q->execute([$fileId]);$file=$q->fetch();
  if(!$file)return null;
  if((int)$file['usuario_id']!==$userId && ($file['carpeta_id']===null || !(new Carpeta($this->db))->canRead((int)$file['carpeta_id'],$userId)))return null;
  return $file;
 }
 public function delete(int $fileId,int $userId):bool {
  $q=$this->db->prepare('DELETE FROM archivos WHERE id=? AND usuario_id=?');$q->execute([$fileId,$userId]);return $q->rowCount()>0;
 }
}

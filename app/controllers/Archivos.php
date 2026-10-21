<?php
namespace MyBox\Controllers;
use MyBox\Config\Database;
use MyBox\Models\Archivo;
final class Archivos {
 private Archivo $model;
 public function __construct(){$this->model=new Archivo(Database::connection());}
 public function listForUser(int $userId,?int $folderId=null):array{return $this->model->listForUser($userId,$folderId);}
 public function upload(int $userId,?int $folderId,array $file,string $comment=''):int{return $this->model->upload($userId,$folderId,$file,$comment);}
 public function findById(int $fileId,int $userId):?array{return $this->model->findById($fileId,$userId);}
 public function delete(int $fileId,int $userId):bool{return $this->model->delete($fileId,$userId);}
}

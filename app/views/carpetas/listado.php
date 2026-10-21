<!doctype html>
<html lang="es"><head><meta charset="utf-8"><title>MyBox - Carpetas</title><meta name="viewport" content="width=device-width, initial-scale=1"></head><body>
<h1>MyBox</h1><p>Hola, <?= h($_SESSION['nombre']??'') ?></p>
<form method="post" action="index.php?action=logout"><?= csrf() ?><button>Salir</button></form>
<nav><a href="index.php?action=carpetas">Mis carpetas</a> | <a href="index.php?action=compartidas">Compartidas conmigo</a></nav>
<h2><?= $current===null?'Mi espacio':'Contenido de la carpeta' ?></h2>
<?php if($current!==null):?><p><a href="index.php?action=carpetas">Volver a raíz</a></p><?php endif; ?>
<h3>Carpetas</h3><ul>
<?php foreach($carpetas as $c):?><li><a href="index.php?action=carpetas&folder=<?= (int)$c['id'] ?>"><?= h($c['nombre']) ?></a> (<?= h($c['propietario']) ?>)
<?php if((int)$c['usuario_id']===$userId):?>
<form style="display:inline" method="post" action="index.php?action=borrar-carpeta"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><input type="hidden" name="folder" value="<?= h($current??'') ?>"><button onclick="return confirm('¿Borrar carpeta y todo su contenido?')">Borrar</button></form>
<form method="post" action="index.php?action=compartir"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><input name="usuario" placeholder="Usuario destinatario" required maxlength="50"><button>Compartir</button></form>
<?php endif; ?></li><?php endforeach; ?></ul>
<?php if($current===null || (new \MyBox\Models\Carpeta(\MyBox\Config\Database::connection()))->owns($current,$userId)): ?>
<h3>Nueva carpeta</h3><form method="post" action="index.php?action=crear-carpeta"><?= csrf() ?><input type="hidden" name="folder" value="<?= h($current??'') ?>"><input name="nombre" maxlength="100" required><button>Crear</button></form>
<h3>Subir archivo (máximo 20 MiB)</h3><form method="post" action="index.php?action=subir" enctype="multipart/form-data"><?= csrf() ?><input type="hidden" name="folder" value="<?= h($current??'') ?>"><input type="file" name="archivo" required><input name="comentario" maxlength="50" placeholder="Comentario"><button>Subir</button></form>
<?php endif; ?>
<h3>Archivos</h3><table border="1" cellpadding="6"><tr><th>Nombre</th><th>Comentario</th><th>Tipo</th><th>MB</th><th>Fecha</th><th>Acciones</th></tr>
<?php foreach($archivos as $a):?><tr><td><?= h($a['nombre']) ?></td><td><?= h($a['comentario']) ?></td><td><?= h($a['tipo']) ?></td><td><?= number_format((int)$a['tamano']/1048576,2) ?></td><td><?= h($a['fecha_subida']) ?></td><td><a href="index.php?action=descargar&id=<?= (int)$a['id'] ?>">Ver / descargar</a>
<?php if((int)$a['usuario_id']===$userId):?><form method="post" action="index.php?action=borrar-archivo"><?= csrf() ?><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><input type="hidden" name="folder" value="<?= h($current??'') ?>"><button>Borrar</button></form><?php endif; ?></td></tr><?php endforeach; ?></table>
</body></html>

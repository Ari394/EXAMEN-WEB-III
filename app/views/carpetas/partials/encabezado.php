<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($tituloPagina ?? 'MyBox') ?> - MyBOX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
          crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/style_01.css">
    <link rel="stylesheet" href="../styles/mybox.css">
</head>
<body class="mybox-app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php?action=carpetas">MyBOX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menuMyBox" aria-controls="menuMyBox"
                    aria-expanded="false" aria-label="Mostrar menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuMyBox">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link<?= ($seccion ?? '') === 'carpetas' ? ' active' : '' ?>"
                           href="index.php?action=carpetas">Mis carpetas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= ($seccion ?? '') === 'compartidas' ? ' active' : '' ?>"
                           href="index.php?action=compartidas">Compartido conmigo</a>
                    </li>
                </ul>
                <span class="navbar-text text-white me-3">
                    <?= h($_SESSION['nombre'] ?? '') ?>
                </span>
                <form method="post" action="index.php?action=logout" class="d-flex">
                    <?= csrf() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm">Salir</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="container mybox-contenido">

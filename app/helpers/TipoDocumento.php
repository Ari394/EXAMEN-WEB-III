<?php
namespace MyBox\Helpers;

// Calcula un tipo de documento legible a partir del MIME guardado en MySQL
// (detectado con finfo al subir) y, si hace falta, de la extensión.
final class TipoDocumento
{
    private const POR_MIME = [
        'application/pdf' => 'PDF',
        'application/msword' => 'Word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
        'application/vnd.oasis.opendocument.text' => 'Documento de texto',
        'application/vnd.ms-excel' => 'Excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel',
        'application/vnd.oasis.opendocument.spreadsheet' => 'Hoja de cálculo',
        'application/vnd.ms-powerpoint' => 'PowerPoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint',
        'application/zip' => 'ZIP',
        'application/x-zip-compressed' => 'ZIP',
        'application/vnd.rar' => 'RAR',
        'application/x-rar' => 'RAR',
        'application/x-rar-compressed' => 'RAR',
        'application/x-7z-compressed' => '7-Zip',
        'application/gzip' => 'GZIP',
        'application/x-tar' => 'TAR',
        'application/json' => 'JSON',
        'application/xml' => 'XML',
        'text/xml' => 'XML',
        'text/csv' => 'CSV',
        'text/html' => 'HTML',
        'text/plain' => 'Texto',
    ];

    private const POR_GRUPO = [
        'image' => 'Imagen',
        'audio' => 'Audio',
        'video' => 'Video',
        'text' => 'Texto',
    ];

    private const POR_EXTENSION = [
        'pdf' => 'PDF',
        'doc' => 'Word',
        'docx' => 'Word',
        'odt' => 'Documento de texto',
        'xls' => 'Excel',
        'xlsx' => 'Excel',
        'ods' => 'Hoja de cálculo',
        'ppt' => 'PowerPoint',
        'pptx' => 'PowerPoint',
        'zip' => 'ZIP',
        'rar' => 'RAR',
        '7z' => '7-Zip',
        'txt' => 'Texto',
        'csv' => 'CSV',
    ];

    // Mismos tipos que el visor (action=visualizar) muestra en el navegador.
    private const VISUALIZABLES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public static function describir(string $mime, string $nombre = ''): string
    {
        $mime = strtolower(trim($mime));
        $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        // Los formatos Office son ZIP por dentro y finfo a veces los
        // reporta como ZIP u octet-stream: la extensión desempata.
        $generico = in_array(
            $mime,
            ['', 'application/zip', 'application/octet-stream'],
            true
        );

        if ($generico && isset(self::POR_EXTENSION[$extension])) {
            return self::POR_EXTENSION[$extension];
        }

        if (isset(self::POR_MIME[$mime])) {
            return self::POR_MIME[$mime];
        }

        [$grupo, $subtipo] = array_pad(explode('/', $mime, 2), 2, '');

        if (isset(self::POR_GRUPO[$grupo])) {
            return self::POR_GRUPO[$grupo] . ' ' . strtoupper($subtipo);
        }

        if ($extension !== '') {
            return 'Archivo ' . strtoupper($extension);
        }

        return 'Desconocido';
    }

    public static function esVisualizable(string $mime): bool
    {
        return in_array(strtolower(trim($mime)), self::VISUALIZABLES, true);
    }
}

<?php

declare(strict_types=1);

namespace BobRoss\Handlers;

use BobRoss\ValueObject\Image;

class LocalFileSystem implements PersistenceHandler
{
    public function persistFile(string $source, string $destinyDir, string $destinyFile) : Image
    {
        $fullPath = $destinyDir . '/' . $destinyFile;
        file_put_contents($fullPath, file_get_contents($source));

        return new Image($fullPath, $destinyFile);
    }

    public function deleteFile(string $filePath) : bool
    {
        unlink($filePath);

        return true;
    }
}

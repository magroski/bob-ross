<?php

declare(strict_types=1);

namespace BobRoss\Handlers;

use BobRoss\ValueObject\Image;

interface PersistenceHandler
{
    public function persistFile(string $source, string $destinyDir, string $destinyFile) : Image;

    public function deleteFile(string $filePath) : bool;
}

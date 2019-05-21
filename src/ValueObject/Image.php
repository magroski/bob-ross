<?php

declare(strict_types=1);

namespace BobRoss\ValueObject;

class Image
{
    /** @var string */
    private $fullPath;
    /** @var string */
    private $fileName;

    public function __construct(string $fullPath, string $fileName)
    {
        $this->fullPath = $fullPath;
        $this->fileName = $fileName;
    }

    public function getFullPath() : string
    {
        return $this->fullPath;
    }

    public function getFileName() : string
    {
        return $this->fileName;
    }
}

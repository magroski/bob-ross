<?php

declare(strict_types=1);

namespace BobRoss\Handlers;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use BobRoss\Utils\S3\Config;
use BobRoss\ValueObject\Image;

class S3 implements PersistenceHandler
{
    /** @var \Aws\S3\S3Client */
    private $s3Client;
    /** @var ?string */
    private $bucketName;

    public function __construct(Config $config, ?string $bucket_name = null)
    {
        $credentials    = new Credentials($config->getAccessKey(), $config->getSecretKey());
        $this->s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $config->getRegion(),
            'credentials' => $credentials,
        ]);

        $this->bucketName = $bucket_name;
    }

    /**
     * Upload a file
     *
     * @param string $source Full path to the file, including the filename
     */
    public function persistFile(string $source, string $destinyDir, string $destinyFile) : Image
    {
        $destinyFile = $this->sanitizeFilename($destinyFile);
        $fullPath    = $this->bucketName . '/' . $destinyDir . '/' . $destinyFile;
        $destinyPath = $destinyDir . '/' . $destinyFile;
        $objectInfo  = [
            'Bucket'     => $this->bucketName,
            'Key'        => $destinyPath,
            'SourceFile' => $source,
            'ACL'        => 'public-read',
        ];

        $this->s3Client->putObject($objectInfo);

        return new Image($fullPath, $destinyFile);
    }

    /**
     * @param string $filePath Url path to the file
     */
    public function deleteFile(string $filePath) : bool
    {
        $this->s3Client->deleteObject(['Bucket' => $this->bucketName, 'Key' => $filePath,]);

        return true;
    }

    public function setBucketName(string $bucketName) : void
    {
        $this->bucketName = $bucketName;
    }

    /**
     * Sanitizes the filename removing not allowed characters by Amazon S3
     */
    private function sanitizeFilename(string $fileName) : string
    {
        return str_replace(["\\", "_", ":", " ", "+"], "-", $fileName);
    }
}

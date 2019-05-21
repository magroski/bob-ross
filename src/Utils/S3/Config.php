<?php

declare(strict_types=1);

namespace BobRoss\Utils\S3;

class Config
{
    /** @var string */
    private $accessKey;
    /** @var string */
    private $secretKey;
    /** @var string */
    private $region;

    public function __construct(string $accessKey, string $secretKey, string $region)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->region    = $region;
    }

    public function getAccessKey() : string
    {
        return $this->accessKey;
    }

    public function getSecretKey() : string
    {
        return $this->secretKey;
    }

    public function getRegion() : string
    {
        return $this->region;
    }
}

<?php

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;
use App\Service\UploaderHelper;

class AppRuntime implements RuntimeExtensionInterface
{
    private $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper) {
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getUploadedAssetPath(string $path): string {
        return $this->uploaderHelper->getPublicPath($path);
    }
}
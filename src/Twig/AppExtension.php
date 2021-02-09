<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use App\Twig\AppRuntime;
use App\Service\UploaderHelper;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('uploaded_asset', [AppRuntime::class, 'getUploadedAssetPath']),
        ];
    }
}
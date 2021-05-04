<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const VIDEO_PATH = 'video';
    const IMAGE_PATH = 'images';

    private $uploadsPath;

    // $uploadsPath is provided by a service parameter in services.yaml
    public function __construct(string $uploadsPath) {
        $this->uploadsPath = $uploadsPath;
    }

    // Move uploaded video file to uploads dir
    // Return the path to the public video uploads dir
    public function uploadVideoFile(UploadedFile $video): string {
        $originalFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = uniqid() . '-' . $originalFilename . '.' . $video->guessExtension();
        $destination = $this->uploadsPath . '/' . self::VIDEO_PATH;

        try {
            $video->move(
                $destination,
                $newFileName
            );
        } catch (FileException $e) {
            dd($e);
        }

        return $newFileName;
    }

    // Move uploaded image file to uploads dir
    // Return the path to the public images uploads dir
    public function uploadImageFile(UploadedFile $image): string {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = uniqid() . '-' . $originalFilename . '.' . $image->guessExtension();
        $destination = $this->uploadsPath . '/' . self::IMAGE_PATH;

        try {
            $image->move(
                $destination,
                $newFileName
            );
        } catch (FileException $e) {
            dd($e);
        }

        return $newFileName;
    }

    public function getPublicPath(string $path): string {
        return '/uploads/' . $path;
    }

    public function getPublicImagePath(string $path): string {
        return '/uploads/' . self::IMAGE_PATH . '/' . $path;
    }
}

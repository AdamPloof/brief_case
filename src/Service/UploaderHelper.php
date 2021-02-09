<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const VIDEO_PATH = 'video';
    private $uploadsPath;

    public function __construct(string $uploadsPath) {
        $this->uploadsPath = $uploadsPath;
    }

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

    public function getPublicPath(string $path): string {
        return '/uploads/' . $path;
    }
}
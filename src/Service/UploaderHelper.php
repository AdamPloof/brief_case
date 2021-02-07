<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    private $uploadsPath;

    public function __construct(string $uploadsPath) {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadVideoFile(UploadedFile $video): string {
        $originalFilename = pathinfo($video->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = uniqid() . '-' . $originalFilename . '.' . $video->guessExtension();
        $destination = $this->uploadsPath .'/video';

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
}
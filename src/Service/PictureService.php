<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class PictureService
{
    public function __construct(
        private ParameterBagInterface $params
    ) {}

    public function square(UploadedFile $picture, ?string $folder = '', ?int $width = 250): string
    {
        // On donne un nouveau nom à l'image
        $file = md5(uniqid(rand(), true)) . '.webp';

        // On récupère les informations de l'image
        $picInfos = getimagesize($picture);  // Récupère les informations de l'image

        if ($picInfos === false) {
            throw new Exception('Format d\'image incorrect');
        }

        // On vérifie le type mime
        switch ($picInfos['mime']) {
            case 'image/png':
                $sourcePic = imagecreatefrompng($picture);
                break;

            case 'image/jpeg':
                $sourcePic = imagecreatefromjpeg($picture);
                break;

            case 'image/webp':
                $sourcePic = imagecreatefromwebp($picture);
                break;

            default:
                throw new Exception('Format d\'image incorrect');
        }

        // On recadre l'image
        $pictureWidth = $picInfos[0];
        $pictureHeight = $picInfos[1];

        switch ($pictureWidth <=> $pictureHeight) {
            case -1:  // portrait
                $squareSize = $pictureWidth;
                $srcX = 0;
                $srcY = ($pictureHeight - $pictureWidth) / 2;
                break;

            case 0:  // carré
                $squareSize = $pictureWidth;
                $srcX = 0;
                $srcY = 0;
                break;

            case 1:  // paysage
                $squareSize = $pictureHeight;
                $srcX = ($pictureWidth - $pictureHeight) / 2;
                $srcY = 0;
                break;
        }

        // On crée une nouvelle image vierge
        $resizedPicture = imagecreatetruecolor($width, $width);

        // On génère le contenu de l'image
        imagecopyresampled($resizedPicture, $sourcePic, 0, 0, $srcX, $srcY, $width, $width, $squareSize, $squareSize);

        // On crée le chemin de stockage
        $path = $this->params->get('uploads_directory') . '/' . $folder;

        // On crée le dossier s'il n'existe pas
        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }

        // On stocke l'image réduite (miniature)
        imagewebp($resizedPicture, $path . '/mini/' . $width . 'x' . $width . '-' . $file);

        // On stocke l'image originale
        $picture->move($path . '/', $file);

        return $file;  // Retourne le nom du fichier
    }
}

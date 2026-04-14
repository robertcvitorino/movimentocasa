<?php

namespace App\Services;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeService
{
    public function generateBase64Png(string $data, int $scale = 4): string
    {
        $options = new QROptions;
        $options->outputType = QROutputInterface::GDIMAGE_PNG;
        $options->eccLevel = EccLevel::L;
        $options->scale = $scale;
        $options->addQuietzone = true;
        $options->outputBase64 = true;

        return (new QRCode($options))->render($data);
    }
}

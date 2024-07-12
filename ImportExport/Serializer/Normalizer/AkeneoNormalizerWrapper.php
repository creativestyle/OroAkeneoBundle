<?php

namespace Creativestyle\Bundle\AkeneoBundle\ImportExport\Serializer\Normalizer;

use Creativestyle\Bundle\AkeneoBundle\Integration\AkeneoChannel;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class AkeneoNormalizerWrapper implements \Symfony\Component\Serializer\Normalizer\DenormalizerInterface
{
    /** @var \Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $fileNormalizer;

    public function __construct(\Symfony\Component\Serializer\Normalizer\DenormalizerInterface $fileNormalizer)
    {
        $this->fileNormalizer = $fileNormalizer;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        $supports = $this->fileNormalizer->supportsDenormalization($data, $type, $format, $context);
        if ($supports) {
            return AkeneoChannel::TYPE === ($context['channelType'] ?? null);
        }

        return $supports;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return $this->fileNormalizer->denormalize($data, $type, $format, $context);
    }
}

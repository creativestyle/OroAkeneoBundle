<?php

namespace Creativestyle\Bundle\AkeneoBundle\ImportExport\Serializer\Normalizer;

use Creativestyle\Bundle\AkeneoBundle\Integration\AkeneoChannel;
use Oro\Bundle\AttachmentBundle\Entity\FileItem;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class FileItemNormalizer implements \Symfony\Component\Serializer\Normalizer\DenormalizerInterface
{
    /** @var \Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $fileNormalizer;

    public function __construct(\Symfony\Component\Serializer\Normalizer\DenormalizerInterface $fileNormalizer)
    {
        $this->fileNormalizer = $fileNormalizer;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return is_a($type, FileItem::class, true)
            && isset($context['channelType'])
            && AkeneoChannel::TYPE === $context['channelType'];
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $fileItem = new FileItem();
        $file = $this->fileNormalizer->denormalize($data, $type, $format, $context);
        $fileItem->setFile($file);

        return $fileItem;
    }
}

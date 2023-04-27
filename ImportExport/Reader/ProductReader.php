<?php

namespace Creativestyle\Bundle\AkeneoBundle\ImportExport\Reader;

use Creativestyle\Bundle\AkeneoBundle\Integration\AkeneoFileManager;
use Oro\Bundle\CacheBundle\Provider\MemoryCacheProviderAwareInterface;
use Oro\Bundle\CacheBundle\Provider\MemoryCacheProviderAwareTrait;
use Oro\Bundle\ImportExportBundle\Context\ContextInterface;

class ProductReader extends IteratorBasedReader implements MemoryCacheProviderAwareInterface
{
    use MemoryCacheProviderAwareTrait;

    /** @var AkeneoFileManager */
    private $akeneoFileManager;

    public function setAkeneoFileManager(AkeneoFileManager $akeneoFileManager): void
    {
        $this->akeneoFileManager = $akeneoFileManager;
    }

    protected function initializeFromContext(ContextInterface $context)
    {
        parent::initializeFromContext($context);

        $items = $this->memoryCacheProvider->get('akeneo_items') ?? [];

        if (!empty($items)) {
            $this->processFileTypeDownload($items, $context);
        }

        $this->stepExecution->setReadCount(count($items));

        $this->setSourceIterator(new \ArrayIterator($items));
    }

    protected function processFileTypeDownload(array $items, ContextInterface $context)
    {
        $this->akeneoFileManager->initTransport($context);

        foreach ($items as $item) {
            foreach ($item['values'] as $values) {
                foreach ($values as $value) {
                    if (empty($value['data'])) {
                        continue;
                    }

                    if (in_array($value['type'], ['pim_catalog_image', 'pim_catalog_file'])) {
                        $this->akeneoFileManager->registerMediaFile($value['data']);
                    }

                    if (in_array($value['type'], ['pim_catalog_asset_collection'])) {
                        foreach ($value['data'] as $data) {
                            $this->akeneoFileManager->registerAssetMediaFile($data);
                        }
                    }

                    if (in_array($value['type'], ['pim_assets_collection'])) {
                        if (!is_array($value['data'])) {
                            continue;
                        }

                        foreach ($value['data'] as $code => $file) {
                            $this->akeneoFileManager->registerAsset($code, $file);
                        }
                    }
                }
            }
        }
    }
}

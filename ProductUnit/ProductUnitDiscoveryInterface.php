<?php

namespace Creativestyle\Bundle\AkeneoBundle\ProductUnit;

use Creativestyle\Bundle\AkeneoBundle\Entity\AkeneoSettings;
use Creativestyle\Bundle\AkeneoBundle\Exceptions\IgnoreProductUnitChangesException;

interface ProductUnitDiscoveryInterface
{
    /**
     * @throws IgnoreProductUnitChangesException
     */
    public function discover(AkeneoSettings $transport, array $importedRecord): array;
}

<?php

namespace Creativestyle\Bundle\AkeneoBundle\ImportExport\Strategy;

interface ExistingEntityAwareInterface
{
    public function getExistingEntity(object $entity, array $searchContext = []): ?object;
}

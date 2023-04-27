<?php

namespace Creativestyle\Bundle\AkeneoBundle\Config;

interface ChangesAwareInterface
{
    public function hasChanges(): bool;
}

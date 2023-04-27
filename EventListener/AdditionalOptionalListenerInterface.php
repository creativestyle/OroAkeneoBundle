<?php

namespace Creativestyle\Bundle\AkeneoBundle\EventListener;

interface AdditionalOptionalListenerInterface
{
    public function enable(): void;

    public function disable(): void;
}

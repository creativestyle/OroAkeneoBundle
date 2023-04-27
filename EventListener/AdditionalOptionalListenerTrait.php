<?php

namespace Creativestyle\Bundle\AkeneoBundle\EventListener;

trait AdditionalOptionalListenerTrait
{
    protected $enabled = true;

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function setEnabled($enabled = true)
    {
        $this->enabled = (bool)$enabled;
    }
}

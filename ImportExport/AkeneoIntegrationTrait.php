<?php

namespace Creativestyle\Bundle\AkeneoBundle\ImportExport;

use Creativestyle\Bundle\AkeneoBundle\Entity\AkeneoSettings;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\IntegrationBundle\Entity\Channel;

/**
 * @property DoctrineHelper $doctrineHelper
 */
trait AkeneoIntegrationTrait
{
    /** @var AkeneoSettings */
    protected $transport;

    private function getTransport(): ?AkeneoSettings
    {
        if ($this->transport) {
            return $this->transport;
        }

        if (!$this->context || false === $this->context->hasOption('channel')) {
            return null;
        }

        $channel = $this->doctrineHelper->getEntityReference(Channel::class, $this->context->getOption('channel'));

        if (!$channel) {
            return null;
        }

        $this->transport = $channel->getTransport();

        return $this->transport;
    }
}

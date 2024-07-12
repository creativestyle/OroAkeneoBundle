<?php

namespace Creativestyle\Bundle\AkeneoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class JsonConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'oro.akeneo.validator.ajax.message';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'oro_akeneo.json_validator';
    }
}

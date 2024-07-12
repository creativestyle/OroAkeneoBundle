<?php

namespace Creativestyle\Bundle\AkeneoBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AttributeMappingConstraint extends Constraint
{
    public $message = 'oro.akeneo.validator.attribute_mapping.message';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'oro_akeneo.attribute_mapping_validator';
    }
}

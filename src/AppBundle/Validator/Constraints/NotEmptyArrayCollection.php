<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class NotEmptyArrayCollection extends Constraint
{
    public $message = 'This value should not be empty.';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}

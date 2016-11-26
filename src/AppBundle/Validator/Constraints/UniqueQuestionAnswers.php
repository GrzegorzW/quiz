<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UniqueQuestionAnswers extends Constraint
{
    public $message = 'Question answers must be unique.';
    public $property;

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}

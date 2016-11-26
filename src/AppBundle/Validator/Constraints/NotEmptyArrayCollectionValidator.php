<?php

namespace AppBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotEmptyArrayCollectionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotEmptyArrayCollection) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\NotEmptyArrayCollection');
        }

        if (!$value instanceof ArrayCollection) {
            return;
        }

        if ($value->count() === 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}

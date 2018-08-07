<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MineRegexValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $arr_result = [];

        $status = preg_match($constraint->pattern, $value, $arr_result);

        if(!$status || $arr_result[0] !== $value){

            $this->context->buildViolation($constraint->message)
                //->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
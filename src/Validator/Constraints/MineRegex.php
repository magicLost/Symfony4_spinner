<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MineRegex extends Constraint
{
    public $message = "Uncorrect characters";
    public $pattern = "/[a-zA-Z0-9_-]*/";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UpdatePassword extends Constraint
{
    public string $message = 'Le mot de passe doit faire 8 caractères et contenir au moins minuscule, une majuscule, un caractère spécial et un chiffre.';
}

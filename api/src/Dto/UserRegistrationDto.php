<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class UserRegistrationDto
{
    #[Assert\Email]
    public $email;
    #[Assert\PasswordStrength(['minScore' => PasswordStrength::STRENGTH_STRONG])]
    public $password;
}

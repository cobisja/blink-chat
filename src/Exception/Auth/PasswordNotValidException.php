<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use Exception;

class PasswordNotValidException extends Exception
{
    protected $message = 'Password not valid';
}

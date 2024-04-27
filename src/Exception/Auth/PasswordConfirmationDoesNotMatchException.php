<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use Exception;

class PasswordConfirmationDoesNotMatchException extends Exception
{
    protected $message = 'Password confirmation does not match';
}
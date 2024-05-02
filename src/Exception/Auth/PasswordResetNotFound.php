<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use Exception;

class PasswordResetNotFound extends Exception
{
    protected $message = "Password Reset Not Found";
}
<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use Exception;

class ResetTokenExpiredException extends Exception
{
    protected $message = "Reset token expired";
}
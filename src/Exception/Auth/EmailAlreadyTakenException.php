<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use Exception;

class EmailAlreadyTakenException extends Exception
{
    protected $message = 'Email already taken';
}
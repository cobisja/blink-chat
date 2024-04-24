<?php

declare(strict_types=1);

namespace App\Exception\User;

use Exception;

class UserNotFoundException extends Exception
{
    protected $message = 'User not found';
}
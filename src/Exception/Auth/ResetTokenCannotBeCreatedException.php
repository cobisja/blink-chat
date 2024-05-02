<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use Exception;

class ResetTokenCannotBeCreatedException extends Exception
{
    protected $message = "Reset token cannot be created";
}
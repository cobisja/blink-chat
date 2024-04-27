<?php

declare(strict_types=1);

namespace App\Exception\Auth;

use Exception;

class NicknameAlreadyTakenException extends Exception
{
    protected $message = 'Nickname already taken';
}
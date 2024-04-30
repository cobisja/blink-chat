<?php

declare(strict_types=1);

namespace App\Exception\Shared;

use Exception;

class NicknameCouldNotBeGeneratedException extends Exception
{
    protected $message = 'Nickname could not be generated';
}

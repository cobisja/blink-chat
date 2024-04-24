<?php

declare(strict_types=1);

namespace App\Controller\Api\Transformer\User;

use App\Entity\User;

class UserTransformer
{
    public function transform(User $user): array
    {
        $userData = [...$user->data()];
        $userData['created_at'] = $user->getCreatedAt()->getTimestamp();

        return $userData;
    }
}

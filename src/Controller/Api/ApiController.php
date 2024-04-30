<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @method User|null getUser()
 */
abstract class ApiController extends AbstractController
{
    use HandleTrait;

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function query($message)
    {
        return $this->handleMessage($message, getReturnedValue: true);
    }

    public function dispatch($message, bool $getReturnedValue = false)
    {
        return $this->handleMessage($message, $getReturnedValue);
    }

    /**
     * @throws \JsonException
     */
    protected function decodeRequest(Request $request)
    {
        return json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    private function handleMessage($message, bool $getReturnedValue = false)
    {
        try {
            $returnedValue = $this->handle($message);

            if ($getReturnedValue) {
                return $returnedValue;
            }
        } catch (HandlerFailedException $error) {
            throw $error->getPrevious() ?? $error;
        }
    }
}

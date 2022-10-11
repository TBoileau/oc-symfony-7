<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Doctrine\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class KernelSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'], // @codeCoverageIgnore
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = match ($exception::class) {
            BadRequestHttpException::class => 400,
            NotFoundHttpException::class => 404,
            default => 500, // @codeCoverageIgnore
        };

        if ($exception instanceof ValidationFailedException) {
            $violations = [];
            foreach ($exception->getViolations() as $violation) {
                $violations[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }
            $event->setResponse(
                new JsonResponse(
                    [
                        'code' => 422,
                        'message' => $exception->getMessage(),
                        'violations' => $violations,
                    ],
                    422
                )
            );

            return;
        }

        if (
            $exception instanceof AccessDeniedHttpException
            && $exception->getPrevious() instanceof AccessDeniedException
            && $exception->getPrevious()->getSubject() instanceof User
        ) {
            $event->setResponse(
                new JsonResponse(
                    [
                        'code' => 404,
                        'message' => sprintf(
                            'User with id "%s" not found.',
                            $exception->getPrevious()->getSubject()->getId()
                        ),
                    ],
                    404
                )
            );

            return;
        }

        $event->setResponse(
            new JsonResponse(
                [
                    'code' => $statusCode,
                    'message' => $exception->getMessage(),
                ],
                $statusCode
            )
        );
    }
}

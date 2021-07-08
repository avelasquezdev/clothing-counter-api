<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * JWTResponseListener.
 *
 * @author Antoine Bluchet <abluchet@ds-restauration.com>
 */
class JWTResponseListener
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Add public data to the authentication response.
     *
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }
        $userRepository = $this->doctrine->getRepository(User::class);
        //$userRepository->invalidate($user->getUsername());

        $event->setData($userRepository->toLoginObject($data, $user));
    }
}
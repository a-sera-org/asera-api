<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 21/01/2024
 */

namespace App\Event\Listener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    /**
     * Add supplement data.
     *
     * @param AuthenticationSuccessEvent $event
     *
     * @return void
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        /** @var $user User|UserInterface */
        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = [
            'id' => $user->getId(),
            'username' => $user->getusername(),
            'roles' => $user->getRoles(),
            'sex' => $user->getSex(),
            'contact' => $user->getContact(),
            'profile_picture' => $user->getMedia() ? $user->getMedia()->getProfilePicture() : '',
            'couverture_image' => $user->getMedia() ? $user->getMedia()->getCoverPicture() : '',
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
        ];

        $event->setData($data);
    }
}

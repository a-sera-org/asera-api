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
            'sex' => $user->getSex(),
            'email' => $user->getContact() ? $user->getContact()->getEmail() : '',
            'tel' => $user->getContact() ? implode(',', $user->getContact()->getPhones() ?? []) : '',
            'profile_picture' => $user->getMedia() ? $user->getMedia()->getProfilePicture() : '',
            'couverture_image' => $user->getMedia() ? $user->getMedia()->getCoverPicture() : '',
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'roles' => $user->getRoles(),
        ];

        $event->setData($data);
    }
}

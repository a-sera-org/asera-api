<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\Api;

use App\Entity\User;
use App\Handler\UserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiRecruiterController extends AbstractController
{
    public function __construct(private readonly UserHandler $userHandler)
    {
    }

    public function __invoke(User $user): User
    {
        return $this->userHandler->handleRecruiter($user);
    }
}

<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\Api;

use App\Entity\Company;
use App\Entity\User;
use App\Handler\UserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiUserCollaboratorController extends AbstractController
{
    public function __construct(private readonly UserHandler $userHandler)
    {
    }

    public function __invoke(User $user, Company $company): User
    {
        return $this->userHandler->addCollaborator($user, $company);
    }
}

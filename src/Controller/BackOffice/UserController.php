<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\BackOffice;

use App\Repository\UserRepository;
use App\Utils\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users', name: 'admin_user_')]
class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private Paginator $paginator)
    {
    }

    #[Route('/list', name: 'list')]
    public function listAllUsers(Request $request): Response
    {
        $query = $this->userRepository->findAllUsers();
        $paginator = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('backoffice/users/list_all_user.html.twig', ['paginator' => $paginator]);
    }
}

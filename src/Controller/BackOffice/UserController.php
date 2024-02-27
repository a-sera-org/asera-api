<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\BackOffice;

use App\Entity\User;
use App\Handler\UserHandler;
use App\Repository\UserRepository;
use App\Utils\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

    #[Route('/admin/users', name: 'admin_user_')]
class UserController extends AbstractController
{
    public function __construct(private UserHandler $userHandler, private UserRepository $userRepository, private Paginator $paginator)
    {
    }

    #[Route('/list', name: 'list')]
    public function listAllUsers(Request $request): Response
    {
        $query = $this->userRepository->findAllUsers();
        $paginator = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render(
            'backoffice/users/list_all_user.html.twig',
            [
                'paginator' => $paginator,
                'menu_user' => true,
            ]
        );
    }

    #[Route('/update/{id}/statut', name: 'statut')]
    public function changeUserStatus(User $user): Response
    {
        try {
            $user->setIsEnabled(!$user->isIsEnabled());
            $this->userRepository->save($user, true);
            $this->addFlash('success', 'Modification éfféctuée avec success !');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'Modification non éfféctuée, details: '.$exception->getMessage());
        }

        return $this->redirectToRoute('admin_user_list');
    }

    #[Route('/{id}/details', name: 'details', methods: 'GET')]
    public function renderUserDetails(User $user): Response
    {
        $allowUpdate = false;
        if ($user->getUserIdentifier() === $this->getUser()->getUserIdentifier()) {
            $allowUpdate = true;
        }

        return $this->render('backoffice/users/user_details.html.twig', ['user' => $user, 'allowUpdate' => $allowUpdate]);
    }

    #[Route('/{id}/update', name: 'update')]
    public function updateUser(Request $request, User $user)
    {
        try {
            $this->userHandler->updateThisUser($request, $user);
            $this->addFlash('success', 'Modification éfféctuée avec success !');
        } catch (\Exception $exception) {
            $this->addFlash('error', 'Modification non éfféctuée, details: '.$exception->getMessage());
        }

        return $this->redirectToRoute('admin_user_list');
    }
}

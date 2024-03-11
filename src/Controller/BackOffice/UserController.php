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
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
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
    public function listAllUsers(Request $request, DataTableFactory $dataTableFactory): Response
    {
        // Créez une instance DataTable à partir du factory
        $dataTable = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => 'ID'])
            ->add('username', TextColumn::class, ['label' => 'Username'])
            ->add('firstname', TextColumn::class, ['label' => 'Firstname'])
            ->add('lastname', TextColumn::class, ['label' => 'Lastname'])
            ->add('contact.email', TextColumn::class, ['label' => 'Email'])
            ->add('contact.phones', TextColumn::class, ['label' => 'Phones'])
            ->add('sex', TextColumn::class, ['label' => 'Sex'])
            // Ajoutez d'autres colonnes selon vos besoins
            ->createAdapter(ORMAdapter::class, [
                'entity' => User::class,
            ]);

        // Gérez la requête DataTable
        $dataTable->handleRequest($request);

        // Si la requête DataTable est prête à être rendue
        if ($dataTable->isCallback()) {
            return $dataTable->getResponse();
        }

        $query = $this->userRepository->findAllUsers();
        $paginator = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render(
            'backoffice/users/list_all_user.html.twig',
            [
                'paginator' => $paginator,
                'menu_user' => true,
                'datatable' => $dataTable,
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

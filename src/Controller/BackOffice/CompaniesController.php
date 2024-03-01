<?php

/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Utils\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/companies', name: 'admin_company_')]
class CompaniesController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private CompanyRepository $companyRepository, private Paginator $paginator)
    {
    }

    #[Route('/list', name: 'list')]
    public function listAllCompanies(Request $request): Response
    {
        $query = $this->companyRepository->findAllCompanies();
        $paginator = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render(
            'backoffice/companies/list_companies.html.twig',
            [
                'paginator' => $paginator,
                'menu_company' => true,
            ]
        );
    }

    #[Route('/update/{id}/statut', name: 'update_statut')]
    public function changeStatut(Company $company): RedirectResponse
    {
        $company->setIsEnabled(!$company->isIsEnabled());
        $this->entityManager->flush();
        $this->addFlash('success', 'Modification éffectué avec success !');

        return $this->redirectToRoute('admin_company_list');
    }

    #[Route('/details/{id}/render', name: 'details', methods: 'GET')]
    public function renderDetails(Company $company)
    {
        return $this->render('backoffice/companies/company_details.html.twig', ['company' => $company]);
    }

    #[Route('/companies/{id}/add-collaborator', name: 'add_collaborator', methods: 'POST')]
    public function addCollaborator(Request $request, Company $company): Response
    {
        $data = json_decode($request->getContent(), true);
        $collaborator = $this->entityManager->getRepository(User::class)->find($data['id']);

        if ($collaborator) {
            $company->addCollaborator($collaborator);
            $this->entityManager->flush();

            $this->addFlash('success', 'Ajout de collaborateur dans l\'entreprise éffectué avec success !');

            return $this->redirectToRoute('admin_company_list');
        }

        $this->addFlash('erreur', 'Echec d\'ajout de collaborateur dans l\'entreprise !');

        return $this->redirectToRoute('admin_company_list');
    }

    #[Route('/companies/{id}/remove-collaborator/{collaboratorId}', name: 'remove_collaborator', methods: 'DELETE')]
    public function removeCollaborator(Company $company, User $collaborator): Response
    {
        $company->removeCollaborator($collaborator);
        $this->entityManager->flush();

        $this->addFlash('success', 'Collaborateur supprimé dans l\'entreprise avec success !');

        return $this->redirectToRoute('admin_company_list');
    }

    #[Route('/remove/contact/{user}/{company}', name: 'remove_user')]
    public function removeContact(User $user, Company $company)
    {
        if ($company->getAdmins() && (1 === $company->getAdmins()->count())) {
            $this->addFlash('error', 'Vous ne pouvez pas faire cette action, l\'entreprise doive avoir au moins un utilisateur avec !');

            return $this->redirectToRoute('admin_company_list');
        }

        $company->removeCollaborator($user);
        $company->removeAdmin($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'Utilisateur supprimé dans l\'entreprise !');

        return $this->redirectToRoute('admin_company_list');
    }
}

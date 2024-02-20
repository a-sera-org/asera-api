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
    public function changeStatut(Company $company)
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

    #[Route('/remove/contact/{user}/{company}', name: 'remove_user')]
    public function removeContact(User $user, Company $company)
    {
        if ($company->getAdmins() && (1 === $company->getAdmins()->count())) {
            $this->addFlash('error', 'Vous ne pouvez pas faire cette action, l\'entreprise doive avoir au moins un utilisateur avec !');

            return $this->redirectToRoute('admin_company_list');
        }

        $company->removeAdmin($user);
        $this->entityManager->flush();
        $this->addFlash('success', 'Utilisateur supprimé sur l\'entreprise !');

        return $this->redirectToRoute('admin_company_list');
    }
}

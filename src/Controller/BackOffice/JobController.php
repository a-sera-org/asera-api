<?php

/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 18/02/2024
 */

namespace App\Controller\BackOffice;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Utils\Paginator;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/job', name: 'admin_job_')]
class JobController extends AbstractController
{
    public function __construct(private readonly JobRepository $jobRepository, private readonly Paginator $paginator, private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/list', name: 'list', methods: 'GET')]
    public function listAllJobs(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $dataTable = $dataTableFactory->create()
            ->add('id', TextColumn::class, ['label' => 'ID'])
            ->add('title', TextColumn::class, ['label' => 'Title'])
            ->add('description', TextColumn::class, ['label' => 'Description'])
            ->add('salary', TextColumn::class, ['label' => 'Salary'])
            ->add('diploma', TextColumn::class, ['label' => 'Diploma'])
            ->add('experiences', TextColumn::class, ['label' => 'Experiences'])
            ->add('company.name', TextColumn::class, ['label' => 'Company'])
            ->add('contract', TextColumn::class, ['label' => 'Contract'])
            ->add('workType', TextColumn::class, ['label' => 'Work Type'])
            ->add('jobCategory', TextColumn::class, ['label' => 'Job Category'])
            ->add('isEnabled', TextColumn::class, ['label' => 'Is Enabled'])
            ->add('createdBy.username', TextColumn::class, ['label' => 'Created By'])
            ->add('updatedBy.username', TextColumn::class, ['label' => 'Updated By'])
            ->add('createdAt', TextColumn::class, ['label' => 'Created At'])
            ->add('updatedAt', TextColumn::class, ['label' => 'Updated At'])
            ->add('deletedAt', TextColumn::class, ['label' => 'Deleted At'])

            // Ajoutez d'autres colonnes selon vos besoins
            ->createAdapter(ORMAdapter::class, [
                'entity' => Job::class,
            ]);

        // Gérez la requête DataTable
        $dataTable->handleRequest($request);

        // Si la requête DataTable est prête à être rendue
        if ($dataTable->isCallback()) {
            return $dataTable->getResponse();
        }

        $queryAllJobs = $this->jobRepository->findAllJobs();
        $paginator = $this->paginator->paginate($queryAllJobs, $request->query->getInt('page', 1));

        return $this->render('backoffice/jobs/list_jobs.html.twig', ['menu_job' => true, 'paginator' => $paginator, 'datatable' => $dataTable]);
    }

    #[Route('/update/{id}/statut', name: 'update_statut', methods: 'GET')]
    public function updateJobStatut(Job $job): Response
    {
        $job->setIsEnabled(!$job->isIsEnabled());
        $this->jobRepository->save($job);
        $this->addFlash('success', 'Statut de l\'emploi modifié avec success !');

        return $this->redirectToRoute('admin_job_list');
    }

    #[Route('/details/{id}', name: 'details', methods: 'GET')]
    public function getJobDetails(Job $job)
    {
        $job_category = $this->translator->trans('job.category.'.$job->getJobCategory().'.label');
        $job_type = $this->translator->trans('job.type.'.$job->getContract().'.label');
        $work_type = $this->translator->trans('job.workType.'.$job->getWorkType().'.label');

        return $this->render('backoffice/jobs/details.html.twig', [
            'job' => $job,
            'jobCategory' => $job_category,
            'jobType' => $job_type,
            'workType' => $work_type,
        ]);
    }
}

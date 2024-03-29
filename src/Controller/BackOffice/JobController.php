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
    public function listAllJobs(Request $request): Response
    {
        $queryAllJobs = $this->jobRepository->findAllJobs();
        $paginator = $this->paginator->paginate($queryAllJobs, $request->query->getInt('page', 1));

        return $this->render('backoffice/jobs/list_jobs.html.twig', ['menu_job' => true, 'paginator' => $paginator]);
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

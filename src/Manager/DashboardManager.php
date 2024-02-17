<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Manager;

use App\Entity\Company;
use App\Entity\Job;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\NoReturn;

class DashboardManager
{
    public function __construct(private EntityManagerInterface $entityManager, private UserRepository $userRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getDashboardPayload(): array
    {
        $userCount = $this->userRepository->count(['isEnabled' => true]);
        $totalUsers = $this->userRepository->count([]);
        $percentUsers = empty($totalUsers) ? 0 : ($userCount * 100 / $totalUsers);

        $jobsCount = $this->entityManager->getRepository(Job::class)->count(['isEnabled' => true]);
        $totalJobs = $this->entityManager->getRepository(Job::class)->count([]);
        $percentJobs = empty($totalJobs) ? 0 : ($jobsCount * 100 / $totalJobs);

        $companiesCount = $this->entityManager->getRepository(Company::class)->count(['isEnabled' => true]);
        $companiesTotal = $this->entityManager->getRepository(Company::class)->count([]);
        $percentCompanies = empty($companiesTotal) ? 0 : ($companiesCount * 100 / $companiesTotal);

        $boardMembers = $this->userRepository->loadUserByRole('ROLE_ADMIN');
        $percentBoards = empty($boardMembers) ? 0 : ($boardMembers * 100 / $totalUsers);

        return [
            'users_count' => $userCount,
            'users_percent' => $percentUsers,
            'jobs_count' => $jobsCount,
            'jobs_percent' => $percentJobs,
            'companies_count' => $companiesCount,
            'companies_percent' => $percentCompanies,
            'boards_count' => $boardMembers,
            'boards_percent' => $percentBoards,
        ];
    }
}

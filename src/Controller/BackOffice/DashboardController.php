<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 14/02/2024
 */

namespace App\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class DashboardController.
 */
#[Route('/admin/', name: 'admin_')]
class DashboardController extends AbstractController
{
    #[Route('dashboard', name: 'dashboard')]
    public function dashboard()
    {
        return $this->render('backoffice/dashboard/index.html.twig');
    }
}

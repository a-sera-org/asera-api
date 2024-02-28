<?php
/**
 * @author julienrajerison5@gmail.com jul
 *
 * Date : 17/02/2024
 */

namespace App\Controller\MiddleController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'middle_page', methods: 'GET')]
    public function homePage(): RedirectResponse
    {
        return $this->redirectToRoute('app_login');
    }
}

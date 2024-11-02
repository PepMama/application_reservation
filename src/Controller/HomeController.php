<?php

namespace App\Controller;

use App\Entity\Services;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ServicesRepository $repository): Response
    {
        $services = $repository->findAll();
        return $this->render('home/index.html.twig', [
            'services' => $services,
        ]);
    }
}

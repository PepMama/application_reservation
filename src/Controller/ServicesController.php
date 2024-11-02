<?php

namespace App\Controller;

use App\Entity\Services;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServicesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ServicesRepository $repository): Response
    {
        $services = $repository->findAll();
        return $this->render('service/indexServices.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/services/{id}', name: 'service_show')]
    public function show(int $id, ServicesRepository $servicesRepository): Response
    {
        $service = $servicesRepository->find($id);

        if (!$service) {
            throw $this->createNotFoundException("Service non trouvé.");
        }
        $timeSlots = $this->generateTimeSlots();

        return $this->render('service/show.html.twig', [
            'service' => $service,
            'services' => $servicesRepository->findAll(),
            'timeSlots' => $timeSlots,
        ]);
    }

    private function generateTimeSlots(): array
    {
        $timeSlots = [];
        $openingHour = 8;
        $closingHour = 18;

        foreach (range(1, 5) as $dayOfWeek) {
            $dailySlots = [];
            for ($hour = $openingHour; $hour < $closingHour; $hour++) {
                if ($hour === 12) {
                    continue;
                }

                $startTime = (new \DateTimeImmutable())->setTime($hour, 0);
                $endTime = (new \DateTimeImmutable())->setTime($hour + 1, 0);

                $dailySlots[] = [
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'isReserved' => false, // Initialement libre
                ];
            }
            $timeSlots[$dayOfWeek] = $dailySlots;
        }

        return $timeSlots;
    }
}

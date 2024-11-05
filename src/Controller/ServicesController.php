<?php
namespace App\Controller;

use App\Entity\Services;
use App\Entity\Booking;
use App\Form\ServicesFormType;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServicesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ServicesRepository $repository): Response
    {
        $services = $repository->findAll();
        return $this->render('service/DisplayListServices.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/services/{id}', name: 'service_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ServicesRepository $servicesRepository, EntityManagerInterface $em): Response
    {
        $services = $servicesRepository->find($id);

        if (!$services) {
            throw $this->createNotFoundException("Service non trouvé.");
        }

        $timeSlots = $this->generateTimeSlots($services, $em);

        return $this->render('service/SlotAvailable.html.twig', [
            'services' => $services,
            'timeSlots' => $timeSlots,
        ]);
    }

    #[Route('/services/create', name:'service_create')]
    public function createService(Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = new Services();
        $form = $this->createForm(ServicesFormType::class, $service);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($service);
            $entityManager->flush();

            return $this->render('service/FormConfirmCreateService.html.twig', [
                'name' => $service->getName(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice(),
            ]);
        }

       return $this->render('service/FormCreateService.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function generateTimeSlots(Services $service, EntityManagerInterface $em): array
    {
        $timeSlots = [];
        $openingHour = 8;
        $closingHour = 18;

        foreach (range(1, 5) as $dayOfWeek) {
            $dailySlots = [];

            for ($hour = $openingHour; $hour < $closingHour; $hour++) {
                if ($hour === 12) {
                    continue; // Pause déjeuner
                }

                $startTime = (new \DateTimeImmutable())
                    ->modify("next Monday")
                    ->modify("+$dayOfWeek day")
                    ->setTime($hour, 0);
                $endTime = $startTime->modify('+1 hour');

                $existingBooking = $em->getRepository(Booking::class)->findOneBy([
                    'service' => $service,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'isReserved' => true,
                ]);

                if (!$existingBooking) {
                    $dailySlots[] = [
                        'startTime' => $startTime,
                        'endTime' => $endTime,
                        'isReserved' => false,
                    ];
                }
            }
            $timeSlots[$dayOfWeek] = $dailySlots;
        }

        return $timeSlots;
    }
}

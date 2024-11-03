<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Services;
use App\Entity\Booking;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/new/{id}/{startTime}/{endTime}', name: 'user_create')]
    public function create(int $id, string $startTime, string $endTime, Request $request, EntityManagerInterface $entityManager): Response
    {
        $service = $entityManager->getRepository(Services::class)->find($id);
        if (!$service) {
            throw $this->createNotFoundException("Service non trouvé.");
        }

        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail(),
            ]);

            if ($existingUser) {
                $user = $existingUser;
            } else {
                $entityManager->persist($user);
                $entityManager->flush();
            }

            // Créer une réservation pour ce service avec les heures de début et de fin spécifiques
            $booking = new Booking();
            $booking->setUser($user);
            $booking->setService($service);

            $startTimeObj = new \DateTime($startTime);
            $endTimeObj = new \DateTime($endTime);
            $dayOfWeek = (int) $startTimeObj->format('N');

            $booking->setStartTime($startTimeObj);
            $booking->setEndTime($endTimeObj);
            $booking->setDayOfWeek($dayOfWeek);
            $booking->setReserved(true);

            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->render('booking/confirm_reservation.html.twig', [
                'user' => $user,
                'service' => $service,
                'booking' => $booking,
            ]);
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
            'startTime' => $startTime,
            'endTime' => $endTime,
        ]);
    }
}

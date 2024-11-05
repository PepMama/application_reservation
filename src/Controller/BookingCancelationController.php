<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookingCancelationController extends AbstractController
{
    #[Route('/cancelation', name: 'app_booking_cancelation')]
    public function index(): Response
    {
        return $this->render('booking/Cancelation.html.twig');
    }

    #[Route('/cancelation/resultats', name: 'booking_cancel_results', methods: ['POST'])]
    public function cancelResults(Request $request, EntityManagerInterface $entityManager): Response
    {
        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);

        if (!$user) {
            $this->addFlash('error', 'Aucun utilisateur trouvé avec ce nom et prénom.');
            return $this->redirectToRoute('booking_cancel_search');
        }

        $bookings = $entityManager->getRepository(Booking::class)->findBy([
            'user' => $user,
            'isReserved' => true,
        ]);

        return $this->render('booking/CancelResult.html.twig', [
            'bookings' => $bookings,
            'user' => $user,
        ]);
    }

    #[Route('/annulation/confirm/{id}', name: 'booking_cancel_confirm')]
    public function cancelConfirm(int $id, EntityManagerInterface $entityManager): Response
    {
        $booking = $entityManager->getRepository(Booking::class)->find($id);

        if (!$booking || !$booking->isReserved()) {
            $this->addFlash('error', 'Réservation introuvable ou déjà annulée.');
            return $this->redirectToRoute('app_booking_cancelation');
        }

        $entityManager->remove($booking);
        $entityManager->flush();

        $this->addFlash('success', 'La réservation a été annulée avec succès.');
        return $this->redirectToRoute('app_home');
    }
}

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
    #[Route('/user/new/{id}', name: 'user_create')]
    public function create(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le service à réserver en fonction de l'ID passé en paramètre
        $service = $entityManager->getRepository(Services::class)->find($id);
        if (!$service) {
            throw $this->createNotFoundException("Service non trouvé.");
        }

        // Créer un nouvel utilisateur et associer le formulaire
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Créer une réservation pour ce service
            $booking = new Booking();
            $booking->setUser($user);
            $booking->setService($service);
            $booking->setStartTime(new \DateTime()); // Définir l'heure de début
            $booking->setEndTime((new \DateTime())->modify('+1 hour')); // Définir l'heure de fin
            $booking->setReserved(true);

            // Sauvegarder la réservation
            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été confirmée !');

            // Redirection après réservation
            return $this->redirectToRoute('service_show', ['id' => $id]);
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'service' => $service,
        ]);
    }
}

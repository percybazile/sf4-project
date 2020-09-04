<?php

namespace App\Controller;

use App\Entity\Events;
use App\Form\DeleteFormType;
use App\Form\UserPasswordFormType;
use App\Form\UserProfileFormType;
use App\Form\EventsFormType;
use App\Form\ModifDeleteFormType;
use App\Repository\EventsRepository;
use App\Security\Voter\ModifDeleteVoter;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="user_profil")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        // Formulaire de modifications des infos du profil
        $profileForm = $this->createForm(UserProfileFormType::class, $this->getUser());
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
        }

        return $this->render('profile/profil_user.html.twig', [
            'profile_form' => $profileForm->createView(),
        ]);
    }
    

    
    /**
     * @Route("/create", name="create_events")
     */
    public function eventsAdd(Request $request, EntityManagerInterface $entityManager)
    {
        $eventsForm = $this->createForm(EventsFormType::class);
        $eventsForm->handleRequest($request);

        if ($eventsForm->isSubmitted() && $eventsForm->isValid()) {
            $events = $eventsForm->getData();
            
            $events->setAuteur($this->getUser());
            
    
            $entityManager->persist($events);
            $entityManager->flush();
            $this->addFlash('success', 'Nouvel events enregistré.');
            return $this->redirectToRoute('home');
        }

        return $this->render('events/create_events.html.twig', [
            'title' => 'Nouvel events',
            'events_form' => $eventsForm->createView(),
        ]);
    }

     
    /**
     * @Route("/events", name="created_events")
     */
    public function createdPage(EventsRepository $repository)
    {
        return $this->render('events/created_events.html.twig', [
            'created_events' => $repository->findAll(),
        ]);
    }

    /**
     * Page d'une event
     * @Route("/events/{id}", name="events_page")
     */
    public function eventsPage(Events $events, Request $request, EntityManagerInterface $entityManager)
    {
        // Afficher le formulaire uniquement si l'utilisateur est connecté
        if ($this->getUser()) {


            // Traitement du formulaire
            $modifdeleteForm = $this->createForm(ModifDeleteFormType::class);
            $modifdeleteForm->handleRequest($request);

            if ($modifdeleteForm->isSubmitted() && $modifdeleteForm->isValid()) {
                $entityManager->persist($modifdeleteForm);
                $entityManager->flush();

                $this->addFlash('success', 'Votre modification a bien été enregistrée');
                return $this->redirectToRoute('events_page', ['id' => $events->getId()]);
            }
        }

        return $this->render('events/events_page.html.twig', [
            'events' => $events,
            'modifdelete_form' => isset($modifdeleteForm) ? $modifdeleteForm->createView() : null,
        ]);
    }

    /**
     * Page de suppression d'un evenement
     * @Route("/modif/{id}/delete", name="modif_delete")
     * @IsGranted("MODIF_DELETE", subject="modifdelete")
     */
    public function deleteNote(Events $events, Request $request, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createForm(DeleteFormType::class);
        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $entityManager->remove($events);
            $entityManager->flush();

            $this->addFlash('info', 'L\'events a été supprimée');
            return $this->redirectToRoute('events_page');
        }
    }














    /**
     * @Route("/myevents", name="my_events")
     */
}

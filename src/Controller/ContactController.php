<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{

    /**
     * @Route("/", name="app_contact_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact, true);

           // return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);

           //Envoi email
           
           //Si le formulaire est valide, on va vider le formilaire
           $contact = new Contact();
           $form = $this->createForm(ContactType::class, $contact);

           //Envoie de message flash pour dire que notre formulaire a été bien envoyer
           $this->addFlash('contact_success', 'Your message has been send. An advisor will answer your very quickly!');
        }
        
        // Si le formilaire est envoyé , mais n'est pas valide, on va envoyer un petit message.
        if($form->isSubmitted() && !$form->isValid()){
            $this->addFlash('contact_error', 'The form contains errors.Please correct and try again.');
        }

        return $this->renderForm('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

}

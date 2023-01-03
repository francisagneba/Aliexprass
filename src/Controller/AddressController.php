<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Nous allons preciser que pour acceder à la route /address, l'utilisateur doit etre obligatoirement 
//connecté, comme ça on pourra le récuperer avant de soumettre le formulaire de son adresse.
//En gros c'est lorqu'il est connecté qu'il pourra créer son adresse via le formulaire.
//Cela se fait dans le fichier security.yml(config/package/security.yml) 

/**
 * @Route("/address")
 */
class AddressController extends AbstractController
{
    /**
     * @Route("/", name="app_address_index", methods={"GET"})
     */
    public function index(AddressRepository $addressRepository): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $addressRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_address_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AddressRepository $addressRepository, CartServices $cartServices): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = $this->getUser(); //Cette ligne nous recupère l'utilisateur qui est connecté
            $address->setUser($user);
            $addressRepository->add($address, true);

            //On verifie si son panier contient desproduits? si oui on le dirige vers checkout
            if ($cartServices->getFullCart()) {
                return $this->redirectToRoute('app_checkout'); 
            }

            $this->addFlash('address_message', 'Your address has been saved');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_address_show", methods={"GET"})
     */
    //public function show(Address $address): Response
    //{
        //return $this->render('address/show.html.twig', [
           // 'address' => $address,
        //]);
   // }

    /**
     * @Route("/{id}/edit", name="app_address_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Address $address, AddressRepository $addressRepository): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addressRepository->add($address, true);

            $this->addFlash('address_message', 'Your address has been edited');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_address_delete", methods={"POST"})
     */
    public function delete(Request $request, Address $address, AddressRepository $addressRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $addressRepository->remove($address, true);

            $this->addFlash('address_message', 'Your address has been deleted');
        }

        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }
}

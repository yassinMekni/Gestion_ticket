<?php

namespace App\Controller;

use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="ticket")
     */
    public function index(): Response
    {
        return $this->render('ticket/templat.html.twig', [
            'controller_name' => 'TicketController',
        ]);
    }

    /**
     * @Route("/ticket/ajouter", name="ajouter_ticket")
     * Method({"GET","POST"})
     */
    public function ajouterTicket(Request $request)
    {
        $ticket = new Ticket();
        $form = $this->createFormBuilder($ticket)
            ->add('titre', TextType::class)
            ->add('nom', TextType::class)
            ->add('discription', TextType::class)
            ->add('statut', TextType::class)
            ->add('dateCr', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Ajouter'))->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();
            $EntityManager = $this->getDoctrine()->getManager();
            $EntityManager->persist($ticket);
            $EntityManager->flush();
            return $this->redirectToRoute('ticket.list');

        }
        return $this->render('operations/ajouter.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @Route("/ticket/modifier/{id}", name="modifier_ticket")
     * Method({"GET","POST"})
     */
    public function modifierTicket(Request $request, $id)
    {
        $ticket = new Ticket();
        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);
        $form = $this->createFormBuilder($ticket)
            ->add('titre', TextType::class)
            ->add('nom', TextType::class)
            ->add('discription', TextType::class)
            ->add('statut', TextType::class)
            ->add('dateCr', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'modifier'))->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();
            $EntityManager = $this->getDoctrine()->getManager();
            $EntityManager->flush();
            return $this->redirectToRoute('ticket.list');
        }
        return $this->render('operations/modifier.html.twig', ['form' => $form->createView()]);
    }
    /**
     * @Route("/ticket/supprimer/{id}", name="supprimer_ticket")
     * Method({"DELETE"})
     */
    public function delete(Request $request,$id)
    {
        $article = $this->getDoctrine()->getRepository(Ticket::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('ticket.list');
    }
    /**
     * @Route("/ticket/details/{id}" , name="details_ticket")
     */
    public function detailTicket($id){
        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);
        return $this->render('operations/detail.html.twig',array('ticket'=>$ticket));
    }

    /**
     * @Route("/{numPage?1}/{limit?9}", name="ticket.list")
     */
    public function listAllTickets($numPage, $limit) {

        $repository = $this->getDoctrine()
            ->getRepository(Ticket::class);


        $tickets = $repository->findAll();
        $nbTickets = count($tickets);
        $nbrePages = ($nbTickets % $limit) ? ceil($nbTickets / $limit) : $nbTickets / $limit;
        if ($numPage > $nbrePages) {
            $numPage = $nbrePages;
        }
        $offset = ($numPage - 1) * $limit;
        $ticketsToShow = array_slice($tickets, $offset, $limit);


        return $this->render('operations/afficher.html.twig', [
            'tickets' => $ticketsToShow,
            'nbrePage' => $nbrePages,
            'page' => $numPage,
            'limit' => $limit
        ]);
    }


}

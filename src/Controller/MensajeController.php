<?php

namespace App\Controller;

use App\Entity\Mensajes;
use App\Form\MensajeType;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MensajeController extends AbstractController
{
    /**
     * @Route("/mensaje", name="mensaje")
     * @return Response
     */
    public function index(Request $request,ManagerRegistry $doctrine, \Swift_Mailer $mailer,LoggerInterface $logger): Response
    {
        $mensaje = new Mensajes();
        $form = $this->createForm(MensajeType::class,$mensaje);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();
        if($form->isSubmitted() && $form->isValid()){


            $entityManager->persist($mensaje);
            $entityManager->flush();



            $message = (new \Swift_Message($form->get('asunto')->getData()))
                ->setFrom('alopezc65@informatica.iesvalledeljerteplasencia.es')
                ->setTo($form->get('email')->getData())
                ->setBody(

                    "Nombre: {$form->get('nombre')->getData()} \n
                    Apellido: {$form->get('apellido')->getData()} \n
                    Asunto: {$form->get('asunto')->getData()} \n
                    Email: {$form->get('email')->getData()} \n
                    Texto: {$form->get('texto')->getData()}",'text/plain'

                );
            $mailer->send($message);
            $logger->info("Se ha añadido un nuevo mensaje.");
            $this->addFlash('exito','Se ha guardado un nuevo mensaje');
            return $this->redirectToRoute('mensaje');
        }
        return $this->render('mensaje/index.html.twig', [
            'controller_name' => 'MensajeController',
            'formulario' => $form->createView()
        ]);
    }
}

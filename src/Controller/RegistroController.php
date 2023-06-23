<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Form\RegistroType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;



class RegistroController extends AbstractController
{
    /**
     * @Route("/registro", name="registro")
     * @return Response
     */
    public function index(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user= new Usuarios();
        $form = $this->createForm(RegistroType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user->setRoles(['ROLE_USER']);
            //cogemos la clave del formulario
            $plaintextPassword=$form["password"]->getData();
            //encriptamos la clave
            $hashedPassword=$passwordHasher->hashPassword($user,$plaintextPassword);
            //asignamos al usuario la clave ya encriptada
            $user->setPassword($hashedPassword);


            $entityManager = $doctrine->getManager();
            //persistimos (guardamos) el usuario que se esta creando en el formulario
            $entityManager->persist($user);
            //forzamos la persistencia de los datos en la BD
            $entityManager->flush();
            return $this->redirectToRoute('login');
        }
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }
}

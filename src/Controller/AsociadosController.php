<?php

namespace App\Controller;

use App\Entity\Asociados;
use App\Form\AsociadosType;
use App\Form\ImagenType;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AsociadosController extends AbstractController
{
    /**
     * @Route("/asociados", name="asociados")
     * @return Response
     */
    public function index(Request $request,ManagerRegistry $doctrine,SluggerInterface $slugger,LoggerInterface $logger): Response
    {
        $asociado = new Asociados();
        $form = $this->createForm(AsociadosType::class,$asociado);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();
        if($form->isSubmitted() && $form->isValid()){
            $imagesFile = $form->get('logo')->getData();
            if($imagesFile){
                $originalFilename = pathinfo($imagesFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-'. uniqid(). '.'. $imagesFile->guessExtension();

                try{
                    $imagesFile->move(
                        $this->getParameter('asociados_directory'),
                        $newFilename
                    );
                }catch (FileException $e){
                    throw new \Exception('Error al subir la imagen');

                }
                $asociado->setLogo($newFilename);

            }

            $entityManager->persist($asociado);
            $entityManager->flush();
            $logger->info("Se ha añadido un nuevo asociado: ".$form->get('nombre')->getData());
            $this->addFlash('exito','Se ha guardado una nuevo asociado: '.$asociado->getNombre());
            return $this->redirectToRoute('asociados');
        }
        $arrayTodosAsociados = $entityManager->getRepository(Asociados::class)->findAll();
        return $this->render('asociados/index.html.twig', [
            'controller_name' => 'AsociadosController',
            'formulario' => $form->createView(),
            'arrayAsociados' => $arrayTodosAsociados
        ]);
    }
}

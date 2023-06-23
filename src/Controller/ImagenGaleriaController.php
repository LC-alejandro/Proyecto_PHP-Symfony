<?php

namespace App\Controller;


use App\Entity\Categorias;
use App\Entity\Imagenes;
use App\Form\ImagenType;


use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImagenGaleriaController extends AbstractController
{
    /**
     * @Route("/vergaleria/{id}", name = "vergaleria")
     * @param $id
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function verImagen($id,ManagerRegistry $doctrine):Response{
        $entityManager = $doctrine->getManager();
        $imagen = $entityManager->getRepository(Imagenes::class)->find($id);
        if($imagen == null || !is_numeric($id)){
            $imagen = 'novalida';
        }
        return $this->render('imagen_galeria/verImagen.html.twig',
            ['imagen'=>$imagen,
                'id' => $id
            ]);
    }
    /**
     * @Route("/imagen/galeria", name="imagen_galeria")
     * @return Response
     */
    public function index(Request $request,ManagerRegistry $doctrine,SluggerInterface $slugger,LoggerInterface $logger): Response
    {
        $imagen = new Imagenes();
        $form = $this->createForm(ImagenType::class,$imagen);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();
        if($form->isSubmitted() && $form->isValid()){
            $imagesFile = $form->get('nombre')->getData();
            if($imagesFile){
                $fechaActual=date('dmYHis');
                $originalFilename = pathinfo($imagesFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-'. uniqid(). '.'. $imagesFile->guessExtension().'_'.$fechaActual;//Fecha actual

                try{
                    $imagesFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                }catch (FileException $e){
                    throw new \Exception('Error al subir la imagen');

                }
                $imagen->setNombre($newFilename);

            }
            //AUMENTAR EL NUMERO DE IMAGENES
            $categoria = $entityManager->getRepository(Categorias::class)->find($form->get('categoria')->getData());
            $categoria->setNumImagenes($categoria->getNumImagenes()+1);

            $entityManager->persist($imagen);
            $entityManager->flush();
            $logger->info("Se ha añadido una nueva imagen: ".$newFilename);

            $this->addFlash('exito','Se ha guardado una nueva imagen: '.$imagen->getNombre());

            return $this->redirectToRoute('imagen_galeria');
        }
        $arrayTodasImagenes = $entityManager->getRepository(Imagenes::class)->findAll();
        return $this->render('imagen_galeria/index.html.twig', [
            'controller_name' => 'ImagenGaleriaController',
            'formulario' => $form->createView(),
            'arrayImagenes' => $arrayTodasImagenes
        ]);
    }
}

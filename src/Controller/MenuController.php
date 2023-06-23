<?php

namespace App\Controller;

use App\Entity\Asociados;
use App\Entity\Imagenes;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/", name="menu")
     */
    public function index(ManagerRegistry $doctrine,Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        //RECOGER GET
        $categoria = $request->query->get("category");
        if(!isset($categoria)) {
            $arrayImagenesMenu = $entityManager->getRepository(Imagenes::class)->mostrarTodasImagenes(1);
            $categoria = 1;
        }else{
            $arrayImagenesMenu = $entityManager->getRepository(Imagenes::class)->mostrarTodasImagenes(intval($categoria));
        }
        shuffle($arrayImagenesMenu);

        $arrayAsociadosMenu = $entityManager->getRepository(Asociados::class)->mostrarAsociados();
        return $this->render('menu/index.html.twig', [
            'controller_name' => 'MenuController',
            'arrayImagenes' => $arrayImagenesMenu,
            'arrayAsociados' => $arrayAsociadosMenu,
            'categoria' => $categoria
        ]);
    }
}

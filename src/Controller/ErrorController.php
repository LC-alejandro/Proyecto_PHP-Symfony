<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route ("/error",name="error")
     */
    public function index():Response{

        return $this->render("error/errores.html.twig", [
            'controller_name' => 'ErrorController',
        ]);
    }


}

    ?>
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'List_Produit')]
    public function index()
    {
        $test="hello world";
        return $this->render('produit/index.html.twig',['test'=> $test] );
    }
}

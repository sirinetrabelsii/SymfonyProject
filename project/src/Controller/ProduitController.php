<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'List_Produit')]
    public function index( ProduitRepository $prodR)
    {
        // $test="hello world";
        $ListProd = $prodR->findAll();
        // dd($ListProd);
        return $this->render('produit/index.html.twig',['listeProduits'=> $ListProd] );
    }
}

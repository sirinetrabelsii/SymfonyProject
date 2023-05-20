<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'List_Produit')]
    public function index(ProduitRepository $prodR)
    {
        // $test="hello world";
        $ListProd = $prodR->findAll();
        // dd($ListProd);
        return $this->render('produit/index.html.twig', ['listeProduits' => $ListProd]);
    }

    #[Route('/produit/{id}', name: 'show_produit', methods: ['GET'])]
    public function showProduit(Produit $produit)
    {
        dd($produit);
        $reviews = $produit->getReviews();

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'reviews' => $reviews,
        ]);
    }
}

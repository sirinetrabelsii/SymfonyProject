<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Reviews;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ProduitController extends AbstractController
{
    private $client;

    public function __construct(SymfonyHttpClientInterface $client) 
    {
        $this->client = $client;
    }
    
    #[Route('/produit', name: 'List_Produit')]
    public function index(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $res = $this->client->request(
            'GET',
            'https://tech.dev.ats-digital.com/api/products?size=50'
        );

        $data = json_decode($res->getContent(), true);
        $i = 0;
        foreach ($data['products'] as $result) {
            $prod = $data['products'];
            $product = new Produit();
            $product->setName($prod[$i]['productName']);
            $product->setPrice($prod[$i]['price']);
            $product->setCategory($prod[$i]['category']);
            $product->setImage($prod[$i]['imageUrl']);
            $entityManager->persist($product);
            $entityManager->flush();

            $j = 0;
            foreach ($prod[$i]['reviews'] as $res) {
                $rev = $prod[$i]['reviews'];
                $review = new Reviews();
                $review->setNote($rev[$j]['value']);
                $review->setCommentaire(substr($rev[$j]['content'], 0, 255));
                $review->setProduit($product);
                $entityManager->persist($review);
                $entityManager->flush();
                $j++;
            }
            $i++;
        }

        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $paginator = $produitRepository->getPaginatedProducts($limit, $offset);
        $totalProducts = $produitRepository->count([]);
        $totalPages = ceil($totalProducts / $limit);

        return $this->render('produit/index.html.twig', [
            'listeProduits' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }


    #[Route('/produit/{id}', name: 'show_produit', methods: ['GET'])]
    public function showProduit(ProduitRepository $produitRepository, $id)
    {
        $produit = $produitRepository->findBy(['id' => $id]);
        return $this->render('produit/show.html.twig', [
            'produit' => $produit[0]
        ]);
    }
}

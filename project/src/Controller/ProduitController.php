<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Reviews;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    private $client;

    public function __construct(SymfonyHttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/', name: 'List_Produit')]
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

        $search = $request->query->get('search');
        $maxPrice = $request->query->get('max_price');

        $limit = 9;
        $page = $request->query->getInt('page', 1);
        $offset = ($page - 1) * $limit;

        $listeProduits = $produitRepository->getPaginatedProducts($limit, $offset, $search, $maxPrice);
        $totalProducts = $produitRepository->countProducts($search, $maxPrice);
        $totalPages = ceil($totalProducts / $limit);

        return $this->render('produit/index.html.twig', [
            'listeProduits' => $listeProduits,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/produit/{id}', name: 'show_produit', methods: ['GET'])]
    public function showProduit(ProduitRepository $produitRepository, $id)
    {
        $produit = $produitRepository->findOneWithReviews($id);
        return $this->render('produit/show.html.twig', [
            'produit' => $produit
        ]);
    }
}
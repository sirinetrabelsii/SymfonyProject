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

    #[Route('/produit', name: 'List_Produit')]
    public function index(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $search = $request->query->get('search');
        $maxPrice = $request->query->get('max_price');

        $queryBuilder = $produitRepository->createQueryBuilder('p');

        if ($search) {
            $queryBuilder->andWhere('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($maxPrice) {
            $queryBuilder->andWhere('p.price <= :max_price')
                ->setParameter('max_price', $maxPrice);
        }

        $query = $queryBuilder->getQuery();

        $paginator = new Paginator($query);

        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $paginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $totalProducts = $paginator->count();
        $totalPages = ceil($totalProducts / $limit);

        $listeProduits = $paginator->getIterator();

        return $this->render('produit/index.html.twig', [
            'listeProduits' => $listeProduits,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/produit/{id}', name: 'show_produit', methods: ['GET'])]
    public function showProduit(ProduitRepository $produitRepository, $id)
    {
        $produit = $produitRepository->find($id);

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
}

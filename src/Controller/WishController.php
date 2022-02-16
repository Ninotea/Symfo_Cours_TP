<?php

namespace App\Controller;

use App\Contenu\Censurator;
use App\Entity\Wish;
use App\Form\WishFormType;
use App\Repository\WishRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class WishController extends AbstractController
{
    /**
     * @Route("/list",name="wish_list")
     */
    public function list(WishRepository $wishRepository):Response
    {
        $wishes = $wishRepository->findPublishedWishesWithCategories();
        return $this->render('wish/listeIdee.html.twig',[
            'wishes' => $wishes
        ]);
    }

    /**
     * @Route("/list/detail/{idIdee}",name="wish_detail")
     */
    public function detail(int $idIdee, WishRepository $wishRepository) :Response
    {
        $wish = $wishRepository->findOneBy(['id' => $idIdee]);

        return $this->render('wish/detailIdee.html.twig',[
            'wish' => $wish
        ]);
    }

    /**
     *
     * @Route("/list/ajout", name="wish_ajout")
     */
    public function ajout(
        Request $request,
        EntityManagerInterface $entityManager,
        Censurator $censurator
        ):Response
    {
        $wish = new Wish();
        $wish->setDateCreated(new \DateTime());
        $currentUserUsername = $this->getUser()->getName();
        $wish->setAuthor($currentUserUsername);

        $wishForm = $this->createForm(WishFormType::class,$wish);
        $wishForm->handleRequest($request);

        if($wishForm->isSubmitted() && $wishForm->isValid())
        {
            $wish->setIsPublished(true);
            $wish->setDescription($censurator->purify($wish->getDescription()));
            $wish->setTitle($censurator->purify($wish->getTitle()));
            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', 'Votre idée à été correctement ajoutée !');
            return $this->redirectToRoute('wish_detail',[
                'idIdee' => $wish->getId()
            ]);
        }

        return $this->render('wish/ajout.html.twig',[
            'wishForm' => $wishForm->createView()
            ]);
    }
}
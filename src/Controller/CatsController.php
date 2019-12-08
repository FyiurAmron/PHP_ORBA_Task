<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\RandomCatUrlGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatsController extends AbstractController
{
    /**
     * @Route("/")
     *
     * @param RandomCatUrlGetter $randomCatUrlGetter
     *
     * @return Response
     */
    public function randomCat(RandomCatUrlGetter $randomCatUrlGetter): Response
    {
        return $this->render('cats/random.html.twig', [
            'url' => $randomCatUrlGetter->getUrl()
        ]);
    }
}

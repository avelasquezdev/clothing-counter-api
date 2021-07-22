<?php

namespace App\Controller;

use App\Repository\ClotheRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ClotheController extends AbstractController
{
    private $clotheRepository;

    public function __construct(ClotheRepository $clotheRepository)
    {
        $this->clotheRepository = $clotheRepository;
    }

    /**
     * @Route("clothes/{clotheId}/add_impact", name="clothes_impact")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addImpact(Request $request)
    {
        $clotheId = $request->attributes->get('clotheId');

        $clothe = $this->clotheRepository->find($clotheId);
        $clothe->setImpacts($clothe->getImpacts() + 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($clothe);
        $em->flush();

        return new JsonResponse('Impacto añadido', 200);
    }
}

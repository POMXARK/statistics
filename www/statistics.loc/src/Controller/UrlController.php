<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UrlController extends AbstractController
{
    #[Route('/save-urls/')]
    public function saveUrls(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $urls = json_decode($request->getContent(), true)['data'];

        foreach ($urls as $_url) {
            $url = new Url();
            $url->setUrl($_url['url']);
            $url->setCreatedDate(DateTimeImmutable::createFromFormat("YmdHis", ($_url['created_date'])));
            $entityManager->persist($url);
        }
        $entityManager->flush();

        return $this->json(['status' => 200]);
    }

    #[Route('/url-statistics/')]
    public function urlStatistics(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $dateStart = $request->get('date_start');
        $dateEnd = $request->get('date_end');
        $domainName = $request->get('domain_name');

        /** @var UrlRepository $urlRepository */
        $urlRepository = $doctrine->getRepository(Url::class);
        $res = $urlRepository->findAllByUrl($dateStart, $dateEnd, $domainName);

        return $this->json(['status' => 200, 'number_of_unique_urls' => count($res)]);
    }
}
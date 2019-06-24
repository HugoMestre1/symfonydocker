<?php
// src/Controller/CountryController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\CountrySearch;
use Doctrine\ORM\EntityManagerInterface;

class CountryController extends AbstractController
{
    /**
     * @Route("/country", methods={"GET"})
     */
    public function index()
    {
        return $this->render('country/index.html.twig');
    }

    /**
     * @Route("/savesearch", methods={"POST"}, name="savesearch")
     */
    public function saveSearch(Request $request)
    {

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $countryName   = $request->get('countryName');
            $searchCountry = new CountrySearch();
            $searchCountry->setCountryName($countryName);
            $entityManager->persist($searchCountry);
            $entityManager->flush();

            return new JsonResponse(['statusCode' => 200, "response" => "Registo inserido com sucesso"]);
        } catch (\Exception $ex) {
            $content  = array('message' => $ex->getMessage());
            $response = new JsonResponse($content, 500);
            $response->headers->set('Content-Type', 'application/json; Charset=UTF-8');

            return $response;
        }

    }

}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response    {

        //return $this->render("index.html.twig");
        return new Response("Hello world!");
    }

    /**
     * @Route("/xml", name="xml")
     */
    public function xml(Request $request): Response {

        $projectDir = $this->getParameter('kernel.project_dir');

        $dom = new \DOMDocument();
        $dom->load($projectDir . '/assets/xml/message.xml');
        $xpath = new \DOMXPath($dom);

        $content = [
            'message_id' => ''
        ];

        $messageIds = $xpath->query('//*[local-name()="MessageID"]');
        if (count($messageIds) > 0) {
            $content['message_id'] = $messageIds->item(0)->nodeValue;
        }

        return $this->render('xml/xml.html.twig', $content);
    }

    /**
     * @Route("/fastapi", name="fastapi")
     */
    public function fastapi() {

        $client = new Client();
        $response = $client->request('GET', "http://c-sf5-python:8000");
        $body = $response->getBody()->getContents();
        $data = \json_decode($body, true);

        return new Response($data['message']);

    }
}
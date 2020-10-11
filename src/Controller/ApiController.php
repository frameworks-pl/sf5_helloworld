<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 

class ApiController extends AbstractController
{
    /**
     * @Route("/api/v1/note/add", name="add_note", methods={"POST"})
     */
    public function addNote(Request $request) : JsonResponse {

        $data = json_decode($request->getContent(), true);
        
        $entityManager = $this->getDoctrine()->getManager();
        $note = new Note();
        $note->setContent($data['note']);
        
        $entityManager->persist($note);
        $entityManager->flush();         

        return new JsonResponse(['status' => 'OK'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/v1/note/delete", name="delete_node", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteNote(Request $request) : JsonResponse {
    
        $data = json_decode($request->getContent(), true);

        if (is_array($data) && array_key_exists('id', $data)) {
            $entityManager = $this->getDoctrine()->getManager();            
            $noteToDelete = $entityManager->getRepository(Note::class)->find($data['id']);
            if ($noteToDelete) {
                $entityManager->remove($noteToDelete);
                $entityManager->flush();
                
                return new JsonResponse(['status' => 'OK'], Response::HTTP_OK);
            }            
        }
        
        return new JsonResponse(['status' => 'ERROR'], Response::HTTP_NOT_FOUND);
        
    }

}
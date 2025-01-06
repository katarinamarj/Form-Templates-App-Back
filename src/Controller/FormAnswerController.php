<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\FormTemplate;
use App\Entity\FormAnswer;

class FormAnswerController extends AbstractController
{

    #[Route('/form-answer', name: 'app_form_answer', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['formTemplateId'], $data['answers'])) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }

        $formTemplate = $entityManager->getRepository(FormTemplate::class)->find($data['formTemplateId']);
        if (!$formTemplate) {
            return new JsonResponse(['message' => 'Form template not found'], 404);
        }

        $formAnswer = new FormAnswer();
        $formAnswer->setFormTemplate($formTemplate);
        $formAnswer->setAnswers($data['answers']); 

        $entityManager->persist($formAnswer);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Form answer saved successfully'], 201);
    }

    #[Route('/form-answer', name: 'get_form_answers', methods: ['GET'])]
    public function getAnswers(EntityManagerInterface $entityManager): JsonResponse
    {
        $answers = $entityManager->getRepository(FormAnswer::class)->findAll();
      
        $response = array_map(function ($answer) {
            return [
                'id' => $answer->getId(),
                'formTemplateId' => $answer->getFormTemplate()->getId(),
                'answers' => $answer->getAnswers(),
            ];
        }, $answers);

        return new JsonResponse($response);
    }


}

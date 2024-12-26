<?php

namespace App\Controller;

use App\Entity\FormTemplate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\FormField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


class FormTemplateController extends AbstractController
{
    /*
    #[Route('/form/template', name: 'app_form_template')]
    public function index(): Response
    {
        return $this->render('form_template/index.html.twig', [
            'controller_name' => 'FormTemplateController',
        ]);
    }
    */

    #[Route('/form-templates', name: 'create_form_template', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $formTemplate = new FormTemplate();
        $formTemplate->setName($data['name'] ?? null);
        $formTemplate->setDescription($data['description'] ?? null);

        $errors = $validator->validate($formTemplate);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($formTemplate);
        $entityManager->flush();

        return new JsonResponse(['message' => 'FormTemplate created successfully'], Response::HTTP_CREATED);

    }

    #[Route('/form-templates', name: 'get_all_form_templates', methods: ['GET'])]
    public function getAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $formTemplates = $entityManager->getRepository(FormTemplate::class)->findAll();

        $response = array_map(fn($template) => [
            'id' => $template->getId(),
            'name' => $template->getName(),
            'description' => $template->getDescription(),
        ], $formTemplates);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/form-templates/{id}', name: 'get_form_template', methods: ['GET'])]
    public function getOne(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $formTemplate = $entityManager->getRepository(FormTemplate::class)->find($id);

        if (!$formTemplate) {
            return new JsonResponse(['error' => 'FormTemplate not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $formTemplate->getId(),
            'name' => $formTemplate->getName(),
            'description' => $formTemplate->getDescription(),
        ], Response::HTTP_OK);
    }

    #[Route('/form-templates/{id}', name: 'update_form_template', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $formTemplate = $entityManager->getRepository(FormTemplate::class)->find($id);

        if (!$formTemplate) {
            return new JsonResponse(['error' => 'FormTemplate not found'], Response::HTTP_NOT_FOUND);
        }

        if (!empty($data['name'])) {
            $formTemplate->setName($data['name']);
        }

        if (isset($data['description'])) {
            $formTemplate->setDescription($data['description']);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'FormTemplate updated successfully'], Response::HTTP_OK);
    }


    #[Route('/form-templates/{id}', name: 'delete_form_template', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $formTemplate = $entityManager->getRepository(FormTemplate::class)->find($id);

        if (!$formTemplate) {
            return new JsonResponse(['error' => 'FormTemplate not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($formTemplate);
        $entityManager->flush();

        return new JsonResponse(['message' => 'FormTemplate deleted successfully'], Response::HTTP_NO_CONTENT);
    }


    #[Route('/form-templates/{id}/fields', name: 'add_field', methods: ['POST'])]
    public function addField(int $id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $formTemplate = $entityManager->getRepository(FormTemplate::class)->find($id);

        if (!$formTemplate) {
            return new JsonResponse(['error' => 'FormTemplate not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $field = new FormField();
        $field->setLabel($data['label']);
        $field->setType($data['type']);
        $field->setOptions($data['options'] ?? null);
        $field->setFormTemplate($formTemplate);

        $errors = $validator->validate($field);

        if (count($errors) > 0) {
           $errorMessages = [];
           foreach ($errors as $error) {
               $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($field);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Field added successfully'], Response::HTTP_CREATED);
    }



    #[Route('/form-templates/{templateId}/fields/{fieldId}', name: 'update_field', methods: ['PUT'])]
    public function updateField(int $templateId, int $fieldId, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
    $formTemplate = $entityManager->getRepository(FormTemplate::class)->find($templateId);

    if (!$formTemplate) {
        return new JsonResponse(['error' => 'FormTemplate not found'], Response::HTTP_NOT_FOUND);
    }

    $formField = $entityManager->getRepository(FormField::class)->find($fieldId);

    if (!$formField || $formField->getFormTemplate() !== $formTemplate) {
        return new JsonResponse(['error' => 'Field not found'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['label'])) {
        $formField->setLabel($data['label']);
    }

    if (isset($data['type'])) {
        $formField->setType($data['type']);
    }

    if (isset($data['options'])) {
        $formField->setOptions($data['options']);
    }

    $errors = $validator->validate($formField);

    if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }

    $entityManager->flush();

    return new JsonResponse(['message' => 'Field updated successfully'], Response::HTTP_OK);
}

#[Route('/form-templates/{templateId}/fields/{fieldId}', name: 'delete_field', methods: ['DELETE'])]
public function deleteField(int $templateId, int $fieldId, EntityManagerInterface $entityManager): JsonResponse
{
    $formTemplate = $entityManager->getRepository(FormTemplate::class)->find($templateId);

    if (!$formTemplate) {
        return new JsonResponse(['error' => 'FormTemplate not found'], Response::HTTP_NOT_FOUND);
    }

    $formField = $entityManager->getRepository(FormField::class)->find($fieldId);

    if (!$formField || $formField->getFormTemplate() !== $formTemplate) {
        return new JsonResponse(['error' => 'Field not found'], Response::HTTP_NOT_FOUND);
    }

    $entityManager->remove($formField);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Field deleted successfully'], Response::HTTP_NO_CONTENT);
}

   
}

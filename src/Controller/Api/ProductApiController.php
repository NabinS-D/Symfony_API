<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Traits\ResponseTrait;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/products', name: 'api_products_')]
class ProductApiController extends AbstractController
{
    use ResponseTrait;

    #[Route('/', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, SerializerInterface $serializer): JsonResponse
    {
        $projects = $projectRepository->findAll();
        $data = $serializer->normalize($projects, null, ['groups' => 'project:read']);
        return $this->successResponseWithData('Projects retrieved successfully', $data);
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $project->setCreatedAt(new \DateTimeImmutable());
            $em->persist($project);
            $em->flush();

            return $this->successResponse('Project successfully created!');
        }

        return $this->errorResponseWithErrors((string) $form->getErrors(true));
    }

    #[Route('/update/{id}', name: 'update_project', methods: ['PUT'])]
    public function update(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        // Fetch the existing project by ID
        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->errorResponse('Project not found',404);

        }

        // Get data from the request
        $data = json_decode($request->getContent(), true);

        // Create and submit the form
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit($data, false); // False to prevent overriding all fields

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the updatedAt field to the current time
            $project->setUpdatedAt(new \DateTimeImmutable());

            $em->flush();
            return $this->successResponse('Project successfully updated!');
        }

        // Handle form validation errors
        return $this->errorResponseWithErrors((string) $form->getErrors(true, false));
     
    }

    #[Route('/show/{id}', methods: ['GET'])]
    public function show(ProjectRepository $projectRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $project = $projectRepository->find($id);
        if (!$project) {
            return $this->errorResponse('Project not found',404);
        }
        $data = $serializer->normalize($project, null, ['groups' => 'project:read']);

        return $this->successResponseWithData('Project retrieved successfully', $data);
    }

    #[Route('/delete/{id}', methods: ['DELETE'])]
    public function delete(ProjectRepository $projectRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $project = $projectRepository->find($id);
        $em->remove($project);
        $em->flush();

        return $this->successResponse('Selected Project has been Deleted Successfully!');
    }
}

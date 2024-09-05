<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Traits\ResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/projects')]
class ProjectApiController extends AbstractController
{
    use ResponseTrait;

    #[Route('/', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository, SerializerInterface $serializer): JsonResponse
    {
        $projects = $projectRepository->findAllWithTasks();
        if (!$projects) {
            return $this->errorResponse('No project found, please add a project first.');
        }
        $data = $serializer->normalize($projects, null, ['groups' => 'project:read']);

        return $this->successResponseWithData('Projects retrieved successfully', $data);
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $project = new Project();
        $project->setProject($data['project']);
        $project->setCreatedAt(new \DateTime());
        $em->persist($project);
        $em->flush();

        return $this->successResponse('Project successfully created!');
    }

    #[Route('/update/{id}', methods: ['PUT'])]
    public function update(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        // Fetch the existing project by ID
        $project = $projectRepository->find($id);

        if (!$project) {
            return $this->errorResponse('Project not found', 404);
        }

        // Get data from the request
        $data = json_decode($request->getContent(), true);

        // Check if required data is present
        if (empty($data['project'])) {
            return $this->errorResponse('Project name is required', 400);
        }

        // Update the project properties
        $project->setProject($data['project']);
        $project->setUpdatedAt(new \DateTime());

        // Persist changes to the database
        $em->flush();

        return $this->successResponse('Project successfully updated!');
    }

    #[Route('/show/{id}', methods: ['GET'])]
    public function show(ProjectRepository $projectRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $project = $projectRepository->find($id);
        if (!$project) {
            return $this->errorResponse('Project not found', 404);
        }
        $data = $serializer->normalize($project, null, ['groups' => 'project:read']);

        return $this->successResponseWithData('Project retrieved successfully', $data);
    }

    #[Route('/delete/{id}', methods: ['DELETE'])]
    public function delete(ProjectRepository $projectRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        // Fetch the existing project by ID
        $project = $projectRepository->find($id);
    
        if (!$project) {
            return $this->errorResponse('Project not found', 404);
        }

        // $user = $this->getUser();
    
        // Soft delete the project by setting the deletedAt field
        $project->setDeletedAt(new \DateTime());
        // $project->setDeletedBy();
    
        // Persist the change to ensure it's tracked by Doctrine
        $em->persist($project);
    
        // Handle potential errors during the flush operation
        try {
            $em->flush();
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the project: ' . $e->getMessage(), 500);
        }
    
        return $this->successResponse('Selected Project has been Deleted Successfully!');
    }
    
    
}


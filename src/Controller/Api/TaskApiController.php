<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Traits\ResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/tasks')]
class TaskApiController extends AbstractController
{
    use ResponseTrait;

    #[Route('/', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
    {
        $tasks = $taskRepository->findAllWithProjects();
        if (!$tasks) {
            return $this->errorResponse('No tasks found, please add a task first.');
        }
    
        $data = $serializer->normalize($tasks, null, ['groups' => 'task:read']);

        return $this->successResponseWithData('tasks retrieved successfully', $data);
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Decode the JSON data from the request
        $data = json_decode($request->getContent(), true);
        
        // Create a new Task instance
        $task = new Task();
        $task->setTask($data['task']);
        $task->setDescription($data['description']);

        // Handle project assignment if provided
        if (isset($data['projects']) && is_array($data['projects'])) {
            foreach ($data['projects'] as $projectId) {
                $project = $em->getRepository(Project::class)->find($projectId);
                if ($project) {
                    $task->addProject($project);
                } else {
                    return $this->errorResponse("Project with ID $projectId not found", 404);
                }
            }
        }

        // Persist the new Task and save changes
        $em->persist($task);
        $em->flush();

        return $this->successResponse('Task successfully created!');
    }

    #[Route('/update/{id}', methods: ['PUT'])]
    public function update(Request $request, TaskRepository $taskRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        // Fetch the existing task by ID
        $task = $taskRepository->find($id);

        if (!$task) {
            return $this->errorResponse('Task not found', 404);
        }

        // Get data from the request
        $data = json_decode($request->getContent(), true);

        // Update fields only if they are present in the request
        if (isset($data['task'])) {
            $task->setTask($data['task']);
        }

        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }

        // Handle potential errors during the flush operation
        try {
            $em->flush();
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while updating the task', 500);
        }

        return $this->successResponse('Task successfully updated!');
    }

    #[Route('/show/{id}', methods: ['GET'])]
    public function show(TaskRepository $taskRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $task = $taskRepository->find($id);
        if (!$task) {
            return $this->errorResponse('Task not found', 404);
        }
        $data = $serializer->normalize($task, null, ['groups' => 'task:read']);

        return $this->successResponseWithData('Task retrieved successfully', $data);
    }

    #[Route('/delete/{id}', methods: ['DELETE'])]
    public function delete(TaskRepository $taskRepository, EntityManagerInterface $em, int $id): JsonResponse
    {
        $task = $taskRepository->find($id);
    
        if (!$task) {
            return $this->errorResponse('Task not found', 404);
        }
        
        $em->remove($task);
        $em->flush();
    
        return $this->successResponse('Selected task has been Deleted Successfully!');
    }
    
}

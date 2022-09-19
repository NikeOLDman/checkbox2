<?php

namespace App\Service;

use App\Entity\Tasks;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class TasksFunctions
{
    private $doctrine;
    private $entityManager;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $this->doctrine->getManager();
    }

    // Creating a Task list for a specific user

    public function getTasksList($userId, $tasksFilter, $orderByTask)
    {
        switch ($tasksFilter) {
            case 'all':
                $getTasks = $this->doctrine->getRepository(Tasks::class)->findBy(['user' => $userId, 'deleted' => 0], [$orderByTask => 'ASC']);
                break;
            case 'active':
                $getTasks = $this->doctrine->getRepository(Tasks::class)->findBy(['user' => $userId, 'deleted' => 0, 'checked' => NULL], [$orderByTask => 'ASC']);
                break;
            case 'close':
                $getTasks = $this->doctrine->getRepository(Tasks::class)->findClosedTasks($userId, $orderByTask);
                break;
        }
        return $getTasks;
    }

    // Switching the Task is Cheched / not Checked

    public function checkedTask($userId, $taskId)
    {
        if (!$getTasks = $this->doctrine->getRepository(Tasks::class)->findOneBy(['user' => $userId, 'id' => $taskId]))
            return false;
        if (!$getTasks->getChecked()) {
            $date = new DateTime();
            $getTasks->setChecked($date);
        } else {
            $getTasks->setChecked(NULL);
        }
        $this->entityManager->persist($getTasks);
        $this->entityManager->flush();

        return true;
    }

    public function countActiveTasks($userId)
    {
        $countActiveTasks = $this->doctrine->getRepository(Tasks::class)->findCountActiveTasks($userId) ?: $countActiveTasks = 0;
        return $countActiveTasks;
    }

    public function countAllActiveTasks()
    {
        $countAllActiveTasks = $this->doctrine->getRepository(Tasks::class)->findAllCountActiveTasks() ?: $countAllActiveTasks = 0;
        return $countAllActiveTasks;
    }

    public function countDeadlineTasks($userId)
    {
        $date = new DateTime('1days');
        $countDeadlineTasks = $this->doctrine->getRepository(Tasks::class)->findCountDeadlineTasks($userId, $date) ?: $countDeadlineTasks = 0;
        return $countDeadlineTasks;
    }

    public function countAllDeadlineTasks()
    {
        $date = new DateTime('1days');
        $countAllDeadlineTasks = $this->doctrine->getRepository(Tasks::class)->findCountAllDeadlineTasks($date) ?: $countAllDeadlineTasks = 0;
        return $countAllDeadlineTasks;
    }

    // Delete Task

    public function deleteTask($userId, $taskId)
    {
        if (!$getTasks = $this->doctrine->getRepository(Tasks::class)->findOneBy(['user' => $userId, 'id' => $taskId]))
            return false;
        $getTasks->setDeleted(TRUE);
        $this->entityManager->persist($getTasks);
        $this->entityManager->flush();

        return true;
    }

    // Change Task

    public function changeTask($userId, $taskId)
    {
        if (!$getTasks = $this->doctrine->getRepository(Tasks::class)->findOneBy(['user' => $userId, 'id' => $taskId]))
            return false;
        return $getTasks;
    }

    public function changeTaskApply($userId, $taskId, $title, $description, $deadline)
    {
        $deadline = new DateTime($deadline);
        if (!$getTasks = $this->doctrine->getRepository(Tasks::class)->findOneBy(['user' => $userId, 'id' => $taskId]))
            return false;
        $getTasks->setTitle($title);
        $getTasks->setDescription($description);
        $getTasks->setDeadline($deadline);
        $this->entityManager->persist($getTasks);
        $this->entityManager->flush();
        return true;
    }

    // Create Task

    public function createTask($userId, $title, $description, $deadline)
    {
        $getUser = $this->doctrine->getRepository(User::class)->find($userId);
        $createTime = new DateTime();
        $deadline = new DateTime($deadline);
        $deleted = 0;
        $newTask = new Tasks();
        $newTask->setUser($getUser);
        $newTask->setTitle($title);
        $newTask->setDescription($description);
        $newTask->setDeadline($deadline);
        $newTask->setCreatetime($createTime);
        $newTask->setDeleted($deleted);
        $this->entityManager->persist($newTask);
        $this->entityManager->flush();
        return true;
    }
}

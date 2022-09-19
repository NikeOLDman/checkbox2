<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use App\Service\TasksFunctions;
use App\Service\Popup;


class TasksController extends AbstractController
{
    private $tasksFilter;
    private $orderByTask;

    public function __construct()
    {
        if (!isset($_COOKIE['tasksFilter'])) {
            setcookie('tasksFilter', 'all', 0x7FFFFFFF, '/');
            $this->tasksFilter = 'all';
        } else {
            $this->tasksFilter = $_COOKIE['tasksFilter'];
        }

        if (!isset($_COOKIE['orderByTask'])) {
            setcookie('orderByTask', 'createtime', 0x7FFFFFFF, '/');
            $this->orderByTask = 'createtime';
        } else {
            $this->orderByTask = $_COOKIE['orderByTask'];
        }
    }

    #[Route('/', name: 'app_tasks')]
    public function index(TasksFunctions $tasksFunctions, Popup $popup, Request $request): Response
    {
        $changedTask = [];
        $userId = $this->getUser()->getId();
        $tasksGridByUser = $tasksFunctions->getTasksList($userId, $this->tasksFilter, $this->orderByTask);
        $mainpopup = $request->query->get('popuptask');
        $popupinclude = $popup->mainpopup($mainpopup);
        $countActiveTasks = $tasksFunctions->countActiveTasks($userId);
        $countDeadlineTasks = $tasksFunctions->countDeadlineTasks($userId);

        if ($mainpopup == 'change' || $mainpopup == 'delete') {
            $taskId = $request->query->get('taskId');
            $changedTask = $tasksFunctions->changeTask($userId, $taskId);
        }

        return $this->render('tasks/index.html.twig', [
            'tasksGridByUser' => $tasksGridByUser,
            'mainpopup' => $popupinclude,
            'changedTask' => $changedTask,
            'tasksFilter' => $this->tasksFilter,
            'countActiveTasks' => $countActiveTasks,
            'countDeadlineTasks' => $countDeadlineTasks,
        ]);
    }

    #[Route('/tasksFilter/{tasksFilterChange}', name: 'tasksfilter')]
    public function tasksfilter(string $tasksFilterChange): Response
    {
        switch ($tasksFilterChange) {
            case 'all':
                $this->tasksFilter = 'all';
                break;
            case 'active':
                $this->tasksFilter = 'active';
                break;
            case 'close':
                $this->tasksFilter = 'close';
                break;
        }
        setcookie('tasksFilter', $this->tasksFilter, 0x7FFFFFFF, '/');
        return $this->redirectToRoute('app_tasks');
    }

    #[Route('/orderByTask/{orderByTaskChange}', name: 'orderbytask')]
    public function orderbytask(string $orderByTaskChange): Response
    {
        switch ($orderByTaskChange) {
            case 'title':
                $this->orderByTask = 'title';
                break;
            case 'createtime':
                $this->orderByTask = 'createtime';
                break;
            case 'deadline':
                $this->orderByTask = 'deadline';
                break;
        }
        setcookie('orderByTask', $this->orderByTask, 0x7FFFFFFF, '/');
        return $this->redirectToRoute('app_tasks');
    }

    #[Route('/checktask/{taskId}', name: 'checktask')]
    public function checktask(TasksFunctions $tasksFunctions, int $taskId): Response
    {
        $userId = $this->getUser()->getId();
        $tasksFunctions->checkedTask($userId, $taskId);
        return $this->redirectToRoute('app_tasks');
    }

    #[Route('/deletetask', name: 'deletetask', methods: ['POST'])]
    public function deletetask(TasksFunctions $tasksFunctions, Request $request): Response
    {
        $userId = $this->getUser()->getId();
        $taskId = $request->request->get('taskId');
        $tasksFunctions->deleteTask($userId, $taskId);
        return $this->redirectToRoute('app_tasks');
    }

    #[Route('/changetask', name: 'changetask', methods: ['POST'])]
    public function changetask(TasksFunctions $tasksFunctions, Request $request): Response
    {
        $userId = $this->getUser()->getId();
        $taskId = $request->request->get('taskId');
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $deadline = $request->request->get('deadline');
        $tasksFunctions->changeTaskApply($userId, $taskId, $title, $description, $deadline);
        return $this->redirectToRoute('app_tasks');
    }

    #[Route('/createtask', name: 'createtask', methods: ['POST'])]
    public function createtask(TasksFunctions $tasksFunctions, Request $request): Response
    {
        $userId = $this->getUser();
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $deadline = $request->request->get('deadline');
        $tasksFunctions->createTask($userId, $title, $description, $deadline);
        return $this->redirectToRoute('app_tasks');
    }
}

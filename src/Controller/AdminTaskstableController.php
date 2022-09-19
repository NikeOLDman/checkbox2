<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TasksFunctions;
use App\Service\UsersFunctions;
use App\Service\Popup;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminTaskstableController extends AbstractController
{
    private $tasksFilter;
    private $orderByTask;
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

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

    #[Route('/admin/taskstable', name: 'app_admin_taskstable')]
    public function admin_taskstable(TasksFunctions $tasksFunctions, UsersFunctions $usersFunctions, Popup $popup, Request $request): Response
    {
        $changedTask = [];
        $session = $this->requestStack->getSession();
        $getChangedUser = $request->query->get('changedUser');
        if ($getChangedUser != NULL) $session->set('changedUserId', $getChangedUser);
        $userId = $session->get('changedUserId');
        $editableUserObject = $usersFunctions->getUserName($userId);
        $tasksGridByUser = $tasksFunctions->getTasksList($userId, $this->tasksFilter, $this->orderByTask);
        $mainpopup = $request->query->get('popuptask');
        $popupinclude = $popup->mainpopup($mainpopup);
        $countAllActiveTasks = $tasksFunctions->countAllActiveTasks();
        $countAllDeadlineTasks = $tasksFunctions->countAllDeadlineTasks();
        $countAllUsers = $usersFunctions->countAllUsers();

        if ($mainpopup == 'adminchange' || $mainpopup == 'admindelete') {
            $taskId = $request->query->get('taskId');
            $changedTask = $tasksFunctions->changeTask($userId, $taskId);
        }

        return $this->render('admin_taskstable/index.html.twig', [
            'tasksGridByUser' => $tasksGridByUser,
            'mainpopup' => $popupinclude,
            'changedTask' => $changedTask,
            'tasksFilter' => $this->tasksFilter,
            'countActiveTasks' => $countAllActiveTasks,
            'countDeadlineTasks' => $countAllDeadlineTasks,
            'countAllUsers' => $countAllUsers,
            'editableUserObject' => $editableUserObject,
        ]);
    }

    #[Route('/admin/tasksFilter/{tasksFilterChange}', name: 'admintasksfilter')]
    public function admintasksfilter(string $tasksFilterChange): Response
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
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/orderByTask/{orderByTaskChange}', name: 'adminorderbytask')]
    public function adminorderbytask(string $orderByTaskChange): Response
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
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/checktask/{taskId}', name: 'adminchecktask')]
    public function adminchecktask(TasksFunctions $tasksFunctions, int $taskId): Response
    {
        $session = $this->requestStack->getSession();
        $userId = $session->get('changedUserId');
        $tasksFunctions->checkedTask($userId, $taskId);
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/deletetask', name: 'admindeletetask', methods: ['POST'])]
    public function admindeletetask(TasksFunctions $tasksFunctions, Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $userId = $session->get('changedUserId');
        $taskId = $request->request->get('taskId');
        $tasksFunctions->deleteTask($userId, $taskId);
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/changetask', name: 'adminchangetask', methods: ['POST'])]
    public function adminchangetask(TasksFunctions $tasksFunctions, Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $userId = $session->get('changedUserId');
        $taskId = $request->request->get('taskId');
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $deadline = $request->request->get('deadline');
        $tasksFunctions->changeTaskApply($userId, $taskId, $title, $description, $deadline);
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/createtask', name: 'admincreatetask', methods: ['POST'])]
    public function admincreatetask(TasksFunctions $tasksFunctions, Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $userId = $session->get('changedUserId');
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $deadline = $request->request->get('deadline');
        $tasksFunctions->createTask($userId, $title, $description, $deadline);
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/deleteuserintasks', name: 'deleteuserintasks', methods: ['POST'])]
    public function deleteuser(UsersFunctions $usersFunctions, Request $request): Response
    {
        $deletedUser = $request->request->get('userEditId');
        $usersFunctions->deleteUser($deletedUser);
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/recoveruserintasks', name: 'recoveruserintasks', methods: ['POST'])]
    public function recoveruser(UsersFunctions $usersFunctions, Request $request): Response
    {
        $recoveredUser = $request->request->get('userEditId');
        $usersFunctions->recoverUser($recoveredUser);
        return $this->redirectToRoute('app_admin_taskstable');
    }

    #[Route('/admin/changeuserintasks', name: 'changeuserintasks', methods: ['POST'])]
    public function changeuser(UsersFunctions $usersFunctions, Request $request): Response
    {
        $changedUserId = $request->request->get('userId');
        $changedUserLogin = $request->request->get('login');
        $changedUserPassword = $request->request->get('pw');
        $changedUserUname = $request->request->get('uname');
        $changedUserRole = $request->request->get('adm');
        $usersFunctions->changeUserApply($changedUserId, $changedUserLogin, $changedUserPassword, $changedUserUname, $changedUserRole);
        return $this->redirectToRoute('app_admin_taskstable');
    }
}

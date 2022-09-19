<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use App\Service\TasksFunctions;
use App\Service\UsersFunctions;
use App\Service\Popup;

class AdminController extends AbstractController
{
    private $usersFilter;
    private $orderByUser;

    public function __construct()
    {
        if (!isset($_COOKIE['usersFilter'])) {
            setcookie('usersFilter', 'all', 0x7FFFFFFF, '/');
            $this->usersFilter = 'all';
        } else {
            $this->usersFilter = $_COOKIE['usersFilter'];
        }

        if (!isset($_COOKIE['orderByUser'])) {
            setcookie('orderByUser', 'createtime', 0x7FFFFFFF, '/');
            $this->orderByUser = 'createtime';
        } else {
            $this->orderByUser = $_COOKIE['orderByUser'];
        }
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(TasksFunctions $tasksFunctions, UsersFunctions $usersFunctions, Popup $popup, Request $request): Response
    {
        $changedUser = [];
        $usersGrid = $usersFunctions->getUsersList($this->usersFilter, $this->orderByUser);
        $mainpopup = $request->query->get('popuptask');
        $popupinclude = $popup->mainpopup($mainpopup);
        $countAllActiveTasks = $tasksFunctions->countAllActiveTasks();
        $countAllDeadlineTasks = $tasksFunctions->countAllDeadlineTasks();
        $countAllUsers = $usersFunctions->countAllUsers();

        if ($mainpopup == 'changeuser' || $mainpopup == 'deleteuser' || $mainpopup == 'recoveruser') {
            $userEditId = $request->query->get('userEditId');
            $changedUser = $usersFunctions->changeUser($userEditId);
        }

        return $this->render('admin/index.html.twig', [
            'usersGrid' => $usersGrid,
            'mainpopup' => $popupinclude,
            'changedUser' => $changedUser,
            'usersFilter' => $this->usersFilter,
            'countActiveTasks' => $countAllActiveTasks,
            'countDeadlineTasks' => $countAllDeadlineTasks,
            'countAllUsers' => $countAllUsers,
        ]);
    }

    #[Route('/admin/usersFilter/{usersFilterChange}', name: 'usersfilter')]
    public function usersfilter(string $usersFilterChange): Response
    {
        switch ($usersFilterChange) {
            case 'all':
                $this->usersFilter = 'all';
                break;
            case 'active':
                $this->usersFilter = 'active';
                break;
            case 'deleted':
                $this->usersFilter = 'deleted';
                break;
        }
        setcookie('usersFilter', $this->usersFilter, 0x7FFFFFFF, '/');
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/orderByUser/{orderByUserChange}', name: 'orderbyuser')]
    public function orderbyuser(string $orderByUserChange): Response
    {
        switch ($orderByUserChange) {
            case 'uname':
                $this->orderByUser = 'uname';
                break;
            case 'login':
                $this->orderByUser = 'username';
                break;
            case 'createtime':
                $this->orderByUser = 'createtime';
                break;
        }
        setcookie('orderByUser', $this->orderByUser, 0x7FFFFFFF, '/');
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/deleteuser', name: 'deleteuser', methods: ['POST'])]
    public function deleteuser(UsersFunctions $usersFunctions, Request $request): Response
    {
        $deletedUser = $request->request->get('userEditId');
        $usersFunctions->deleteUser($deletedUser);
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/recoveruser', name: 'recoveruser', methods: ['POST'])]
    public function recoveruser(UsersFunctions $usersFunctions, Request $request): Response
    {
        $recoveredUser = $request->request->get('userEditId');
        $usersFunctions->recoverUser($recoveredUser);
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/createuser', name: 'createuser', methods: ['POST'])]
    public function createuser(UsersFunctions $usersFunctions, Request $request): Response
    {
        $createdUserLogin = $request->request->get('login');
        $createdUserPassword = $request->request->get('pw');
        $createdUserUname = $request->request->get('uname');
        $usersFunctions->createUser($createdUserLogin, $createdUserPassword, $createdUserUname);
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/changeuser', name: 'changeuser', methods: ['POST'])]
    public function changeuser(UsersFunctions $usersFunctions, Request $request): Response
    {
        $changedUserId = $request->request->get('userId');
        $changedUserLogin = $request->request->get('login');
        $changedUserPassword = $request->request->get('pw');
        $changedUserUname = $request->request->get('uname');
        $changedUserRole = $request->request->get('adm');
        $usersFunctions->changeUserApply($changedUserId, $changedUserLogin, $changedUserPassword, $changedUserUname, $changedUserRole);
        return $this->redirectToRoute('app_admin');
    }
}

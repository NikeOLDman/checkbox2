<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFunctions
{
    private $doctrine;
    private $entityManager;
    private $userPasswordHasher;
    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $this->doctrine->getManager();
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function getUsersList($usersFilter, $orderByUser)
    {
        switch ($usersFilter) {
            case 'all':
                $getUsers = $this->doctrine->getRepository(User::class)->findBy([], [$orderByUser => 'ASC']);
                break;
            case 'active':
                $getUsers = $this->doctrine->getRepository(User::class)->findBy(['deleted' => 0], [$orderByUser => 'ASC']);
                break;
            case 'deleted':
                $getUsers = $this->doctrine->getRepository(User::class)->findBy(['deleted' => 1], [$orderByUser => 'ASC']);
                break;
        }
        return $getUsers;
    }

    public function getUserName($userId)
    {
        $getUser = $this->doctrine->getRepository(User::class)->find($userId);
        return $getUser;
    }

    public function countAllUsers()
    {
        $countAllUsers = $this->doctrine->getRepository(User::class)->findCountAllUsers() ?: $countAllUsers = 0;
        return $countAllUsers;
    }

    public function deleteUser($userEditId)
    {
        $deletedUser = $this->doctrine->getRepository(User::class)->find($userEditId);
        $deletedUser->setDeleted(TRUE);
        $deletedUser->setRoles([""]);
        $this->entityManager->persist($deletedUser);
        $this->entityManager->flush();
        return true;
    }

    public function recoveruser($userEditId)
    {
        $recoveredUser = $this->doctrine->getRepository(User::class)->find($userEditId);
        $recoveredUser->setDeleted(FALSE);
        $recoveredUser->setRoles(["ROLE_SUPERUSER"]);
        $this->entityManager->persist($recoveredUser);
        $this->entityManager->flush();
        return true;
    }

    public function changeUser($userEditId)
    {
        $deletedUser = $this->doctrine->getRepository(User::class)->find($userEditId);
        return $deletedUser;
    }

    public function createUser($createdUserLogin, $createdUserPassword, $createdUserUname)
    {
        $date = new DateTime();
        $user = new User();
        $user->setUsername($createdUserLogin);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $createdUserPassword
            )
        );
        $user->setUname($createdUserUname);
        $user->setCreatetime($date);
        $user->setDeleted(false);
        $user->setRoles(['ROLE_SUPERUSER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return true;
    }

    public function changeUserApply($changedUserId, $changedUserLogin, $changedUserPassword, $changedUserUname, $changedUserRole)
    {
        if (!$user = $this->doctrine->getRepository(User::class)->find($changedUserId))
            return false;
        $user->setUsername($changedUserLogin);
        if ($changedUserPassword != '') {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $changedUserPassword
                )
            );
        }
        $user->setUname($changedUserUname);
        if ($changedUserRole == 'on') {
            $user->setRoles(['ROLE_ADMIN']);
        } else {
            $user->setRoles(['ROLE_SUPERUSER']);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return true;
    }
}

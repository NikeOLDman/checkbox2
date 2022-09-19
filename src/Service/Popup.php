<?php

namespace App\Service;

class Popup
{

    public function mainpopup($popuptask)
    {
        $popupinclude = '';
        switch ($popuptask) {
            case NULL:
                break;
            case 'create':
                $popupinclude = "tasks/_createtask.html.twig";
                break;
            case 'delete':
                $popupinclude = "tasks/_deletetask.html.twig";
                break;
            case 'change':
                $popupinclude = "tasks/_changetask.html.twig";
                break;
            case 'checktask':
                include "inc/checktask.inc.php";
                break;
            case 'deleteuser':
                $popupinclude = "admin/_deleteuser.html.twig";
                break;
            case 'recoveruser':
                $popupinclude = "admin/_recoveruser.html.twig";
                break;
            case 'createuser':
                $popupinclude = "admin/_createuser.html.twig";
                break;
            case 'changeuser':
                $popupinclude = "admin/_changeuser.html.twig";
                break;
            case 'admincreate':
                $popupinclude = "admin_taskstable/_createtask.html.twig";
                break;
            case 'admindelete':
                $popupinclude = "admin_taskstable/_deletetask.html.twig";
                break;
            case 'adminchange':
                $popupinclude = "admin_taskstable/_changetask.html.twig";
                break;
            case 'admindeleteuser':
                $popupinclude = "admin_taskstable/_deleteuser.html.twig";
                break;
            case 'adminrecoveruser':
                $popupinclude = "admin_taskstable/_recoveruser.html.twig";
                break;
            case 'adminchangeuser':
                $popupinclude = "admin_taskstable/_changeuser.html.twig";
                break;
        }
        return $popupinclude;
    }
}

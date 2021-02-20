<?php


namespace App\Controllers;

use App\Classes\BaseController;
use App\Classes\Container;
use App\Models\Users;

class SiteController extends BaseController
{
    public function indexAction()
    {
        $app = Container::getInstance();
        $model = new Users;
        if ($app->request->getRealMethod() == 'POST') {
            $phone = htmlspecialchars($app->request->request->get('phone'));
            $email = htmlspecialchars($app->request->request->get('email'));
            if ($model->validateEmail($email)&&$model->validatePhone($phone)) {
                $model->setPhone($phone);
                $model->setEmail($email);
                $model->AddOrUpdate();
            }
        }
        $template = $this->twig->load('index.twig');
        return $template->render(['title' => 'Register page', 'error' => $model->error, 'model' => $model]);
    }

    public function restoreAction()
    {
        $app = Container::getInstance();
        $model = new Users;
        if ($app->request->getRealMethod() == 'POST') {
            $email = htmlspecialchars($app->request->request->get('email'));
            if ($model->validateEmail($email)) {
                if ($model_db = $model->findModel(md5($email))) {
                    $model_db->setEmail($email);
                    if ($model_db->sendEmail()) {
                        $model = $model_db;
                    } else {
                        $model->error[] = 'Message not send.';
                        $model->setEmail('');
                    }
                }
            }
        }
        $template = $this->twig->load('restore.twig');
        return $template->render(['title' => 'Restore page', 'error' => $model->error, 'model' => $model]);
    }
}
<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    public function update(Request $request,ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $manager = $doctrine->getManager();
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            $errors = $form->getErrors(true); // Array of Error
            return $this->render('user/index.html.twig', [
                'user' => $user,
                'errors' => $errors,
            ]);
        }
        if(!empty($request->get('lastName'))){
            $user->setLastName($request->get('lastName'));
        }
        if(!empty($request->get('firstName'))){
            $user->setFirstName($request->get('firstName'));
        }
        if(!empty($request->get('password'))){

        }
        if(!empty($request->get('phone'))){
            $user->setPhone($request->get('phone'));
        }
        if(!empty($request->get('email'))){
            $user->setEmail($request->get('email'));
        }
        $manager->persist($user);
        $manager->flush();
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}

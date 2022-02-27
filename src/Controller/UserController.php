<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    public function update(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $manager = $doctrine->getManager();
        $user = $this->getUser();
        $filesystem = new Filesystem();
        
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
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $request->get('password')
            );
            $user->setPassword($hashedPassword);
        }
        
        $user->setPhone($request->get('phone'));

        if(!empty($request->files->get("formFile"))){
            //dd($user->getEmail());
            $userPath = 'assets/img/'.$user->getEmail();
            $file = $request->files->get("formFile");
            if(!$filesystem->exists($userPath)){
                $filesystem->mkdir($userPath);
            }
            //dd($filesystem->exists($userPath."/profileImage.".$file->getClientOriginalExtension()));
            // if($filesystem->exists($userPath."/profileImage.".$file->getClientOriginalExtension())){
            //     $filesystem->remove([$filesystem->exists($userPath."/profileImage.".$file->getClientOriginalExtension())]);
            // }
            //dd($request->files->get("formFile"));
            if($file->move($userPath,"profileImage.".$file->getClientOriginalExtension())){
                $user->setPicture($user->getEmail()."/profileImage.".$file->getClientOriginalExtension());
            }
        }

        if(!empty($request->get('email'))){
            $userPath = 'assets/img/'.$user->getEmail();
            if(!$filesystem->exists($userPath)){
                $filesystem->mkdir($userPath);
            }
            $oldPath = 'assets/img/'.$user->getEmail();
            $user->setEmail($request->get('email'));
            $userPath = 'assets/img/'.$user->getEmail();
            if(!$filesystem->exists($userPath)){
                $filesystem->rename($oldPath, $userPath);
            }
        }

        $manager->persist($user);
        $manager->flush();
        $this->addFlash('success', "Les informations ont bien été modifiées");
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}

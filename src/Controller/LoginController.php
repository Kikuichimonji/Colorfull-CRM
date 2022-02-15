<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    public function submit(Request $request,ManagerRegistry $doctrine): Response
    {
        
        $session = $request->getSession();
        $post = $request->request;
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $request->request->get("email")]);
        if($user){
            return $this->redirectToRoute('dashboard-index');
        }else{
            return $this->render('login/index.html.twig', [
                'error' => 'Cet email n\'existe pas',
            ]);
        }
        $session->set('user',$post);
        
    }

    public function showRegister(Request $request,UserPasswordHasherInterface $passwordHasher): Response
    {
        return $this->render('login/register.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    public function register(Request $request,UserPasswordHasherInterface $passwordHasher,ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $password = $request->request->get("password");
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $roles= [];
        switch ($request->request->get("roles")) {
            case "admin":
                array_push($roles,"admin");
            case "manager":
                array_push($roles,"manager");
            case "user":
                array_push($roles,"user");
                break;
        }
        $user->setLastName($request->request->get("lastName"));
        $user->setFirstName($request->request->get("firstName"));
        $user->setEmail($request->request->get("email"));
        $user->setRoles(json_encode($roles));
        $user->setPhone($request->request->get("phone"));
        $user->setCreatedAt(new \DateTime('NOW'));
        $user->setPassword($hashedPassword);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('dashboard-index');
    }
}

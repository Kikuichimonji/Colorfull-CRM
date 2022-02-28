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
    
    /**
     * Show the login page
     *
     * @param AuthenticationUtils $authenticationUtils
     * 
     * @return Response
     * 
     */
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


    /**
     * Register a new user 
     *
     * @param Request $request 
     * @param UserPasswordHasherInterface $passwordHasher
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     */
    public function register(Request $request,UserPasswordHasherInterface $passwordHasher,ManagerRegistry $doctrine): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_MANAGER');
        $user = new User();
        $password = $request->request->get("password");
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $roles= ["ROLE_".strtoupper($request->request->get("roles"))];

        $user->setLastName($request->request->get("lastName"));
        $user->setFirstName($request->request->get("firstName"));
        $user->setEmail($request->request->get("email"));
        $user->setRoles($roles);
        $user->setPhone($request->request->get("phone"));
        $user->setCreatedAt(new \DateTime('NOW'));
        $user->setPassword($hashedPassword);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('dashboard-index');
    }
    public function showRegister(): Response
    {
        return $this->render('login/register.html.twig', [
        ]);
    }

    public function admin()
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    }
}

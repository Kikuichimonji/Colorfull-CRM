<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Planning;
use App\Form\UserFormType;
use App\Entity\ContactExtrafields;
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserFormType::class);
        $form->submit($request->request->all());
        $extrafieldsRepository = $doctrine->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();
        

        if (!$form->isValid()) { //validate form info in UserFormType
            $errors = $form->getErrors(true); // Array of Error
            
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            
            return $this->render('admin/index.html.twig', [
                'extrafields' => $extrafields,
            ]);
        }
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(["email" => $request->request->get("email")]);
        if( $user){ //if a user exist with this mail, we return an error
            $this->addFlash('error', "Cet email est déjà utilisé");
            return $this->render('admin/index.html.twig', [
                'extrafields' => $extrafields,
            ]);
        }
        $user = new User();
        $planning = new Planning();
        $password = $request->request->get("password");
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $roles= ["ROLE_".strtoupper($request->request->get("roles"))];

        $planning->setLabel("No title");
        $user->setLastName($request->request->get("lastName"));
        $user->setFirstName($request->request->get("firstName"));
        $user->setEmail($request->request->get("email"));
        $user->setRoles($roles);
        $user->setPhone($request->request->get("phone"));
        $user->setCreatedAt(new \DateTime('NOW'));
        $user->setPassword($hashedPassword);

        $planning->setPlanningOwner($user);
        $user->setPlanning($planning);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', "L'utilisateur a bien été créé");
        return $this->redirectToRoute('admin-index');
    }

    /**
     * Nothing interesting, just some memos
     *
     * @return [type]
     * 
     */
    public function admin()
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    }
}

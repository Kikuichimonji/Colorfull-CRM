<?php

namespace App\Controller;

use App\Entity\ContactExtrafields;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * Show the admin page
     * 
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $extrafieldsRepository = $doctrine->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();
        $users =  $doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'user' => $this->getUser(),
            'extrafields' => $extrafields,
            'users' => $users
        ]);
    }

    /**
     * Add, edit, and remove Extrafields
     * 
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * 
     * @return Response
     */
    public function newExtrafields(ManagerRegistry $doctrine, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $extrafieldsRepository = $doctrine->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();
        $entityManager = $doctrine->getManager();

        $post = $request->request;
        $postArray = $post->all();
        $amountPost = count($postArray)/3; //we receive 3 fields (label,inputType,forCompany) for one extrafield, i could make it dynamic, but i don't have the time
        $amountFields = count($extrafields);

        if($amountPost > $amountFields){ //
            for ($i=0; $i < $amountPost; $i++) { 
                if(isset($postArray["label_". ($i+1)])){
                    $extrafield = isset($extrafields[$i]) ? $extrafields[$i] : $extrafield = new ContactExtrafields();
                    
                    $extrafield->setLabel($postArray["label_". ($i+1)]);
                    $extrafield->setInputType($postArray["inputType_". ($i+1)]);
                    $extrafield->setForCompany($postArray["forCompany_". ($i+1)] == "company" ? 1 : 0);
        
                    isset($extrafields[$i]) ? null : $entityManager->persist($extrafield) ;
                }else{
                    if(isset($extrafields[$i])){
                        $entityManager->remove($extrafields[$i]);
                    }
                }
            }
        }else{ // if we delete more element than we had at the start we have to slightly change some checks and counting
            for ($i=0; $i < $amountFields; $i++) { 
                if(isset($postArray["label_". ($i+1)])){
                    $extrafield = isset($extrafields[$i]) ? $extrafields[$i] : $extrafield = new ContactExtrafields();
                    
                    $extrafield->setLabel($postArray["label_". ($i+1)]);
                    $extrafield->setInputType($postArray["inputType_". ($i+1)]);
                    $extrafield->setForCompany($postArray["forCompany_". ($i+1)] == "company" ? 1 : 0);
        
                    isset($extrafields[$i]) ? null : $entityManager->persist($extrafield) ;
                }else{
                    $entityManager->remove($extrafields[$i]);
                }
            }
        }

        $entityManager->flush();

        $this->addFlash('success', "Les extrafields ont bien été modifiés");
        return $this->redirectToRoute('admin-index');
    }

    // public function error404(): Response
    // {

    //     return $this->render('test/404.html.twig', [

    //     ]);
    // }
}

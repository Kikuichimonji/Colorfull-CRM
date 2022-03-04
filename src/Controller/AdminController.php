<?php

namespace App\Controller;

use App\Entity\ContactExtrafields;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function index(ManagerRegistry $doctrine): Response
    {
        $extrafieldsRepository = $doctrine->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/index.html.twig', [
            'user' => $this->getUser(),
            'extrafields' => $extrafields,
        ]);
    }

    public function newExtrafields(ManagerRegistry $doctrine, Request $request): Response
    {
        $extrafieldsRepository = $doctrine->getRepository(ContactExtrafields::class);
        $extrafields = $extrafieldsRepository->findAll();
        $entityManager = $doctrine->getManager();

        $post = $request->request;
        $postArray = $post->all();

        $explodedKey = explode('_',array_keys($postArray)[count($postArray)-1]);
        if(isset($explodedKey[1])){
            $amount =  $explodedKey[1];
        }else{
            $this->addFlash('error', "Problèmes de découpage des extrafields");
            return $this->render('admin/index.html.twig', [
                'user' => $this->getUser(),
                'extrafields' => $extrafields,
            ]);
        }

        // dd($extrafields);
        //dd($post->all());
        for ($i=0; $i < $amount; $i++) { 
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

        $entityManager->flush();

        $this->addFlash('success', "Les extrafields ont bien été modifiés");
        return $this->redirectToRoute('admin-index');
    }
}

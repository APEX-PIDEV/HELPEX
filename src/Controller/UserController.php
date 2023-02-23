<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\EditYourProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{


   






    //////////////////////backbackbackBABY////////////

    #[Route('admin/users', name: 'AllUsers'), IsGranted("ROLE_ADMIN")]
    public function AllUsers(UserRepository $userRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        

        return $this->render('user/back/allusers.html.twig', [
            'user' => $this->getUser(),
            'usersList' => $userRepo->findAll()
        ]);
    }


    ///////////////////////FRONTBABY/////////////

    #[Route('/professionals', name: 'ProUsers')]
public function ProUsers(UserRepository $userRepo): Response
    {
       // $user = $this->getUser();

        //if (!$user) {
          //  return $this->redirectToRoute('app_login');
       // }
        $role= 'ROLE_PRO' ;




        return $this->render('user/front/professionals.html.twig', [
            'user' => $this->getUser(),
            'ProList' => $userRepo->findPros([$role])
        ]);
    }



    #[Route('professionals/{id}', name: 'showProUser', methods: ['GET'])]
    public function show(User $User): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $User = $entityManager->getRepository(User::class)->find($User);
        

        return $this->render('user/front/ProfileProuser.html.twig', [
            'user' => $User,
            
        ]);
    }

    #[Route('/YourProfile', name: 'YourProfile')]

    public function YourProfile(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }        return $this->render('user/front/YourProfile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/YourProfile/edit/{id}', name: 'YourProfileEdit', methods : ['GET', 'POST'])]

    public function updateYourProfile(Request $request, User $user, UserRepository $ur,  SluggerInterface $slugger ): Response
    {
        $form = $this->createForm(EditYourProfileType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            dump($request->request->all());
            $ur->save($user, true);
            return $this->redirectToRoute('YourProfile', [], Response::HTTP_SEE_OTHER);
        }
        dump($form->getErrors(true, false));
        return $this->renderForm("user/front/EditYourProfile.html.twig", [
            "user" => $user,
            "updateForm" => $form,
            
            
        ]);
    }

    
}
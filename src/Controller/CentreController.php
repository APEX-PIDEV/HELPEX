<?php

namespace App\Controller;

use App\Entity\Centre;
use App\Form\CentreType;
use App\Repository\CentreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/centre')]
class CentreController extends AbstractController
{
    #[Route('/newx', name: 'app_centre_new', methods: ['GET', 'POST']), IsGranted('ROLE_ADMIN')]
    public function new(Request $request, CentreRepository $centreRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $centre = new Centre();
        $form = $this->createForm(CentreType::class, $centre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('imagecentre')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('users_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $centre->setImagecentre($newFilename);
            }
            $centreRepository->save($centre, true);

            return $this->redirectToRoute('app_centre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('centre/new.html.twig', [
            'centre' => $centre,
            'form' => $form,
        ]);
    }
    //***mobilejson**
    /**
     * @Route("/deletecentrejson/{id}", name="delete_centre_json")
     */
    public function deletecentrejson(Request $req, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $centre = $em->getRepository(Centre::class)->find($id);
        $em->remove($centre);
        $em->flush();
        $jsonContent = $Normalizer->normalize($centre, 'json', ['groups' => "post:read"]);
        return new Response("centre deleted successfully " . json_encode($jsonContent));
    }

    #[Route('/allcentress', name: 'mobileaffichercentre', methods: ['GET'])]
    public function indexjson(NormalizerInterface $Normalizer): Response
    {
        $respository=$this->getDoctrine()->getRepository(Centre::class);
        $centres=$respository->findAll();
        $jsonContent=$Normalizer->normalize($centres,'json',['groups'=>'post:read']);
        return  new Response(json_encode($jsonContent));


    }
    #[Route('/ajouterjson',name:'mobileajoutercentre',methods: ['GET','POST'])]
    public function addcentrejson(Request $request,NormalizerInterface $Normalizer)
    {
        $em=$this->getDoctrine()->getManager();
        $centre=new Centre();
        $centre->setNomCentre($request->get('nomCentre'));
        $centre->setAdresseCentre($request->get('adresseCentre'));
        $centre->setEmailCentre($request->get('emailCentre'));
        $centre->setTelephoneCentre($request->get('telephoneCentre'));
        $centre->setSiteWebCentre($request->get('siteWebCentre'));
        $em->persist($centre);
        $em->flush();
        $jsonContent=$Normalizer->normalize($centre,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));


    }

    #[Route("/centrejson/{id}", name: "centreid", methods: ['GET'])]
    public function CentreId($id, NormalizerInterface $normalizer, CentreRepository $repo)
    {
        $centre = $repo->find($id);
        $centreNormalises = $normalizer->normalize($centre, 'json', ['groups' => "post:read"]);
        return new Response(json_encode($centreNormalises));
    }
    /**
     * @Route("/updatecentrejson/{id}", name="update_centre_json")
     */

    public function updateCentreJSON(Request $request, $id, NormalizerInterface $Normalizer)
    {

        $em = $this->getDoctrine()->getManager();
        $centre = $em->getRepository(Centre::class)->find($id);
        $centre->setNomCentre($request->get('nomCentre'));
        $centre->setAdresseCentre($request->get('adresseCentre'));
        $centre->setEmailCentre($request->get('emailCentre'));
        $centre->setTelephoneCentre($request->get('telephoneCentre'));
        $centre->setSiteWebCentre($request->get('siteWebCentre'));

        $em->flush();

        $jsonContent = $Normalizer->normalize($centre, 'json', ['groups' => "post:read"]);
        return new Response("centre updated successfully " . json_encode($jsonContent));
    }


    //*************************************************

    #[Route('/', name: 'app_centre_index', methods: ['GET'] ), IsGranted('ROLE_ADMIN')]
    public function index(CentreRepository $centreRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('centre/index.html.twig', [
            'centres' => $centreRepository->findAll(),
        ]);
    }


    #[Route('/front', name: 'app_centre_index_front', methods: ['GET'])]
    public function indexfront(CentreRepository $centreRepository): Response
    {

        return $this->render('centre/index_front.html.twig', [
            'centres' => $centreRepository->findAll(),
        ]);
    }



    #[Route('/{id}', name: 'app_centre_show', methods: ['GET']), IsGranted('ROLE_ADMIN')]
    public function show(Centre $centre): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('centre/show.html.twig', [
            'centre' => $centre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_centre_edit', methods: ['GET', 'POST']), IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Centre $centre, CentreRepository $centreRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(CentreType::class, $centre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('imagecentre')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('users_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception
                }

                $centre->setImagecentre($newFilename);
            }
            $centreRepository->save($centre, true);

            return $this->redirectToRoute('app_centre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('centre/edit.html.twig', [
            'centre' => $centre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_centre_delete', methods: ['POST']), IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Centre $centre, CentreRepository $centreRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('delete'.$centre->getId(), $request->request->get('_token'))) {
            $centreRepository->remove($centre, true);
        }

        return $this->redirectToRoute('app_centre_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\CaisseOrganisation;
use App\Entity\Organisation;
use App\Repository\CaisseOrganisationRepository;
use App\Repository\OrganisationRepository;
use App\Repository\UserRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DonationsController extends AbstractController
{
    #[Route('/donations', name: 'app_donations')]
    public function index(CaisseOrganisationRepository $caisseOrganisationRepository): Response
    {
        return $this->render('donations/index.html.twig', [
            'Caisses' => $caisseOrganisationRepository->findAll(),
        ]);
    }

    #[Route('/payment/{id}', name: 'app_donations_payment')]
    public function Payment(Request $request,MailerInterface $mailer,CaisseOrganisation $caisseOrganisation,CaisseOrganisationRepository $caisseOrganisationRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        Stripe::setApiKey('sk_test_51MhCUgHv0arDT0U0P19vmMrNfUVhnrgr7oLZC6LOOXnbTEcciLDUPqrehv7UVbWDnCggUNFZegmbAuyK6wzwtEDI00F2fvASZc');
        $amount = $request->query->getInt('amount');
        $session_stripe = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amount*100,
                        'product_data' => [
                            'name' => 'Donation',
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', ['id' => $caisseOrganisation->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $session=$request->getSession();
        $session->set('amount_donated',$amount);


      /*  $email = (new TemplatedEmail());

        $email->subject('THANK YOU !');
        $email->from('apex.pidev1@gmail.com');
        $email->to($user->getUsername());
        $email->htmlTemplate('emails/template.html.twig');
        $email->context([
        'amount' => $amount,
    ]);
        $mailer->send($email);*/

        //dd($session->id);

        return $this->redirect($session_stripe->url,303);
    }


    #[Route('/success-url/{id}', name: 'success_url')]
    public function successUrl(Request $request,CaisseOrganisationRepository $caisseOrganisationRepository,CaisseOrganisation $caisseOrganisation): Response
    {

        $amount_paid = (int)$request->getSession()->get('amount_donated');
        $caisseOrganisation->setMontantCaisseOrg($caisseOrganisation->getMontantCaisseOrg()+floatval($amount_paid));
        $request->getSession()->set('amount_donated',0);
        $caisseOrganisationRepository->save($caisseOrganisation,true);
        return $this->render('donations/success.html.twig', []);
    }


    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('donations/cancel.html.twig', []);
    }

    #[Route('/ListOrgs', name: 'app_donations_organisations')]
    public function ListAllOrgs(OrganisationRepository $organisationRepository): Response
    {
        return $this->render('donations/organisations.html.twig', [
            'organisations' => $organisationRepository->findAll(),
        ]);
    }
    #[Route('/ViewOrg/{id}', name: 'app_donations_ViewOrg')]
    public function ViewOrg(Organisation $organisation, CaisseOrganisationRepository $caisseOrganisationRepository): Response
    {
        return $this->render('donations/showOrganisation.html.twig', [
            'organisation' => $organisation,
            'Caisses' => $caisseOrganisationRepository->findBy([
                'organisation' => $organisation
            ])
        ]);
    }

}

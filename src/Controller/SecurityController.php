<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/inscription", name="security_registration")
     */
     public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
     {
        $user = new User();

         $form = $this->createForm(RegistrationType::class, $user);

         $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid())
         {

            //  en passant simplement le $user à la fonction encodePassword()
            // Symfony comprend qu'il doit utiliser l'algorithme de cryptage défini dans security.yaml car il est implémenté pour la classe User
             $hash = $encoder->encodePassword($user, $user->getPassword());

             $user->setPassword($hash);
            
            // préparation de l'inscription du $user en db
             $manager->persist($user);
            //  inscription du $user en db
             $manager->flush();

             return $this->redirectToRoute('security_login');
         }

          return $this->render('security/registration.html.twig', [
             'form' => $form->createView(),
             'page_title' => 'Blog | Inscription'
         ]);
     }

     /**
      * @Route("/connexion", name="security_login")
      */
     public function login()
     {
         return $this->render('security/login.html.twig', [
             'page_title' => 'Blog | Connexion'
         ]);
     }

     /**
      * @Route("/deconnexion", name="security_logout")
      */
      public function logout()
      {

      }

}

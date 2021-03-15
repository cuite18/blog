<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        // on crée un nouveau exemplaire de l'entité User afin de remplire l'objet via le formuliare et l'insertion en BDD
        $user = new User;

        // On execute la méthode createForm() du SecurityController afin de créer un formulaire par rapport à la classe RegistrationFormType déstiné à remplir les setter de l'objet entité $user
        $formRegistration = $this->createForm(RegistrationFormType::class, $user);

        dump($request);

        // handleRequest() : méthode Symfony qui permet à la validation du formulaire, de remplir l'objet entity $user et d'envoyer les données du formulaire dans les bons setter et propriétés de l'entité $user
        $formRegistration->handleRequest($request); // $_POST ['username']--> setUsername($_POST ['username']);

        if($formRegistration->isSubmitted() && $formRegistration->isValid())
        {
            // SI le formulaire a bien été validé (isSubmitted) et que chaque donnée saisie ont bien été transmise aux bon setter de l'objet (isValid), alors on entre dans le IF

             // on encode le mot de passe
            //$hash contient une clé de hachage du mot de passe
            $hash = $encoder->encodePassword($user, $user->getPassword());

           
            $user->setPassword($hash);

            dump($user);

            $manager->persist($user); // préparation et mise en mémoire de la requette INSERT SQL
            $manager->flush(); // execution de la requette SQL

            $this->addFlash('success', "Bravo !! votre compte a bien été validé ");

            return $this->redirectToRoute('security_login');

        }

        return $this->render('security/registration.html.twig', [
            'formRegistration' => $formRegistration->createView() // on envoi le formulaires sur le templates afin de pouvooirl'afficher en front 
        ]);
    }
    /**
     * methode permettant de se connecter au blog
     * 
     * @Route("/connexion", name="security_login")
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig');

    }

    /**
     * méthode permettent de se deconnecter pas de reponse nous avons juste beoisn de la routes
     * 
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        // cette methode ne retourne rien il nous suffit d'avoir une routte pour se deconnecter
    }

}

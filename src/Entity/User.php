<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *      fields = {"email"},
 *      message = "Compte déja existant avec cet adresse email !!"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     *      
     * @Assert\NotBlank(
     *      message = "saisir une adresse Email valide !"
     * )
     * 
     * @Assert\Email(
     *      message = "saisir une adresse Email valide"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank(
     *      message = "saisir un nom d'utilisateur !"
     * )
     * 
     * @Assert\Length(
     *      min=2,
     *      max=50,
     *      minMessage="Nom d'utilisateur trop court",
     *      maxMessage="Nom d'utilisateur trop long"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\EqualTo(
     *      propertyPath="confirm_password",
     *      message="les mots de passe ne coresponde pas"
     * )
     */
    private $password;

    /**
     * cette propriétés receptionne une valeursmais n'est pas stocker donc pas d'annotation ORM
     * 
     *@Assert\EqualTo(
     *      propertyPath="confirm_password",
     *      message="les mots de passe ne coresponde pas"
     * )
    */
    public $confirm_password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    
    // Pour encoder le mot de passe, l'entité User doit implémenter (similaire de l'héritage) l'interface UserInterface
    // Cette interface contient des méthodes abstraites que nous sommes obligé de déclarer
    //  méthodes obligatoires : getUsername(), getPassword(), eraseCredentials(), getSalt() et getRoles()

    // cette methode est uniquement destinée a nettoyer les mots de passe en texte brut éventuellemnt stockée
    public function eraseCredentials()
    {

    }

    // Renvoi la chaine de caractère non encodé que l'utilisateur a saisi, qui est utilisé à l'origine pour, encoder le mot de passe
    public function getSalt()
    {

    }

    // Cette fonction renvoi un tableau de chaine de caractères
    // Renvoi les rôles accordés à l'utilisateur
    public function getRoles()
    {
        //return ["ROLE_USER"];
        return $this->roles; //
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}

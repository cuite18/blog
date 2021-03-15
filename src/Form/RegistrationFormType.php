<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'required' => false,
            ])

            ->add('username', TextType::class, [
                'required' => false,
            ])

            ->add('password', PasswordType::class, [
                'required' => false,
            ])

            ->add('confirm_password', PasswordType::class, [
                'required' => false,
            ])// on ajoute un champs de 'confirm_password' qui ne sera pas enregistré en Bdd mais juste pour comparer les mots de passe 
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

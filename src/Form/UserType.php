<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Validator\UpdatePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
//            ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new UpdatePassword()
                ],
                'first_options'  => [
                    'label'=>'Mot de passe', 
                    'attr' => array(
                        'placeholder' => '******'
                    ),
                ],
                'second_options' => [
                    'label'=>'Répéter le mot de passe',
                    'attr' => array(
                        'placeholder' => '******'
                    ),
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.'
                ])
            ->add('pseudo', TextType::class)
            ->add('prenom', TextType::class)
            ->add('nom', TextType::class)
            ->add('telephone', TextType::class)
            ->add('photo', FileType::class, ['label'=>'Ma photo', 'required' => false, 'mapped' => false ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ])
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

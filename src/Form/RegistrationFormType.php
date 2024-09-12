<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Campus;
use App\Validator\UpdatePassword;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class)
            ->add('prenom',TextType::class)
            ->add('pseudo',TextType::class)
            ->add('nom',TextType::class)
            ->add('telephone',TelType::class)
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ])
            ->add('plainPassword', RepeatedType::class, [
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

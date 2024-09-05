<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom de la sortie :'
            ])
            ->add('dateSortie', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>'Date et heure de la sortie :'
            ])
            ->add('dateFinInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>"Date limite d'inscription :"
            ])
            ->add('nombrePlace', IntegerType::class, [
                'label'=>'Nombre de places :'
            ])
            ->add('duree', IntegerType::class, [
                'label'=>'Durée :'
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label'=>'Description et infos :'
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus :',
            ])
            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville :',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'label' => 'Lieu :',
            ])

            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier la sortie',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

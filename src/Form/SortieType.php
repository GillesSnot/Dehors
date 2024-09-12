<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
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
        $defaultCampus = $options['default_campus'];

        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom de la sortie :',
                'label_attr' => ['class' => 'form-label']
            ])
            ->add('dateSortie', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>'Date et heure de la sortie :',
            ])
            ->add('dateFinInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>"Date limite d'inscription :",
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
            ->add('campus', EntityType::class, options: [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'data' => $defaultCampus,
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
            ]);

            if ($options['is_edit']) {
                $builder
                    ->add('modifier', SubmitType::class, [
                        'attr' => ['class' => 'btn-dark'],
                        'label' => 'Modifier',
                    ]);
            }


            if (!$options['is_edit']) {
                $builder
                    ->add('enregistrer', SubmitType::class, [
                        'attr' => ['class' => 'btn btn-dark'],
                        'label' => 'Enregistrer',
                    ])
                    ->add('publier', SubmitType::class, [
                    'attr' => ['class' => 'btn btn-dark'],
                    'label' => 'Publier la sortie',
                ]);
            }


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'default_campus' => null,
            'required' => false,
            'is_edit' => false,
        ]);
    }
}

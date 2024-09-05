<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use function Symfony\Component\Clock\now;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultCampus = $options['default_campus'];

        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom de la sortie :'
            ])
            ->add('dateSortie', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>'Date et heure de la sortie :',
            ])
            ->add('dateFinInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>"Date limite d'inscription :",
                'constraints' => [
                    new Callback([$this, 'dateFinInscriptionValidation']),
                ]
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
            ])

            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier la sortie',
                ])
        ;
    }

    public function dateFinInscriptionValidation($dateFinInscription, ExecutionContextInterface $context) {
        $form = $context->getRoot();
        $dateSortie = $form->get('dateSortie')->getData();

        if ($dateFinInscription > $dateSortie) {
            // Ajoute une violation si la validation échoue
            $context->buildViolation("La limite de date d'inscription ne peut pas être postérieure à la date de sortie, voyez vous ?")
                ->atPath('dateFinInscription')  // Cible le champ endDate dans le formulaire
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'default_campus' => null,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Campus;
use App\Form\Model\SortieFilterModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus',
                'required' => true,
            ])
            ->add('recherche', TextType::class, [
                'label'=>'Le nom de la sortie contient',
                'attr' => array(
                    'placeholder' => 'Recherche'
                )
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label'=>'Entre',
            ])
            ->add('dateFin', DateTimeType::class, [
                'label'=>'et',
            ])
            ->add('organisateur', CheckboxType::class, [
                'label'=>'Sorties dont je suis l\'organisateur/trice'
            ])
            ->add('inscrit', CheckboxType::class, [
                'label'=>'Sorties auxquelles je suis inscrit/e'
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label'=>'Sorties auxquelles je ne suis pas inscrit/e'
            ])
            ->add('passee', CheckboxType::class, [
                'label'=>'Sorties passées '
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SortieFilterModel::class,
            'required' => false,
        ]);
    }
}

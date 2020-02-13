<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('duree')
            ->add('dateLimiteInscription', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('nomLieu', TextType::class, [
                'mapped'=>false,
            ])
            ->add('rueLieu', TextType::class, [
                'mapped'=>false,
            ])
            ->add('latitude', TextType::class, [
                'mapped'=>false,
            ])

            ->add('longitude', TextType::class, [
                'mapped'=>false,
            ])
            ->add('ville', TextType::class, [
                'mapped'=>false,
            ])
            ->add('codePostal', TextType::class, [
                'mapped'=>false,
            ])
            ->add('Valider', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

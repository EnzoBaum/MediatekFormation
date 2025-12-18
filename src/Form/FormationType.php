<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire de gestion des formations
 */
class FormationType extends AbstractType
{
    /**
     * Construction du formulaire
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la formation',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description (facultative)',
                'required' => false,
            ])

            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist',
                'placeholder' => 'Sélectionner une playlist',
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez sélectionner une playlist.']),
                ],
            ])

            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Catégories',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ])

            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez indiquer une date.']),
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date ne peut pas être postérieure à aujourd\'hui.',
                    ]),
                ],
            ])

            ->add('videoId', TextType::class, [
                'label' => 'Lien ou ID de la vidéo YouTube',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Exemple : https://www.youtube.com/watch?v=XXXXX',
                ],
            ]);
    }

    /**
     * Options par défaut du formulaire
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}

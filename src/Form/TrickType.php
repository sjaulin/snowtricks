<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\VideoType;
use App\Entity\Category;
use App\Form\PictureType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {

        // TODO Embed Collection of forms : https://symfony.com/doc/current/form/form_collections.html
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'help' => 'Doit avoir entre ' .
                    Trick::CONSTRAINT_NAME_LENGTH_MIN . ' et ' .
                    Trick::CONSTRAINT_NAME_LENGTH_MAX . ' caractères',
            ])
            ->add('description', TextareaType::class, [])

            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => [
                    'label' => false
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])

            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'required' => false,
                'entry_options' => [
                    'label' => false
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]);

        $builder->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Trick;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{

    const PICTURES_MAX_NB = 5;
    const VIDEOS_MAX_NB = 3;

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {

        // TODO Embed Collection of forms : https://symfony.com/doc/current/form/form_collections.html
        // Pas arrivé à le faire
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'help' => 'Doit avoir entre ' .
                    Trick::CONSTRAINT_NAME_LENGTH_MIN . ' et ' .
                    Trick::CONSTRAINT_NAME_LENGTH_MAX . ' caractères',
            ])
            ->add('description', TextareaType::class, []);

        for ($i = 0; $i < self::PICTURES_MAX_NB; $i++) {
            $builder->add('picture_' . $i, FileType::class, [
                'label' => false,
                'help' => 'Format autorisé : jpeg',
                'multiple' => false,
                'mapped' => false, // dot not link to database
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Vous devez envoyer une photo en .jpeg',
                    ])
                ],
            ]);
        }

        for ($i = 0; $i < self::VIDEOS_MAX_NB; $i++) {
            $builder->add('video_' . $i, TextType::class, [
                'label' => false,
                'required' => false,
                'help' => 'Exemple : https://www.youtube.com/embed/ID https://www.dailymotion.com/embed/video/ID',
                'mapped' => false, // dot not link to database
            ]);
        }

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

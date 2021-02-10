<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PictureType extends AbstractType
{
    // https://symfony.com/doc/current/controller/upload_file.html
    // TODO : Read : https://github.com/dustin10/VichUploaderBundle
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1000k',
                        'allowPortrait' => false,
                        'allowPortraitMessage' => 'Vous devez envoyer une photo au format paysage',
                        'mimeTypes' => [
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Vous devez envoyer une photo en .jpeg',
                    ])
                ],
                'help' => 'Photo uniquement au format jpeg'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}

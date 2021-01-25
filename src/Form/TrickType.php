<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrickType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la figure',
                'help' => 'Doit avoir entre ' . Trick::constraint_name_length_min . ' et ' . Trick::constraint_name_length_max . ' caractères',
            ])
            ->add('description', TextareaType::class, [])
            ->add('pictures', FileType::class, [
                'label' => 'Ajouter des photos',
                'help' => 'Sélectionner plusieurs photos si nécessaire',
                'multiple' => true,
                'mapped' => false, // dot not link to bdd
                'required' => false,
            ])
            ->add('category', EntityType::class, [
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

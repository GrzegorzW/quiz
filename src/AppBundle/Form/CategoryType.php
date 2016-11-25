<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'description' => 'Name',
                'constraints' => [
                    new NotBlank()
                ]
            ])->add('status', ChoiceType::class, [
                'description' => 'Status',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    Category::STATUS_ENABLED,
                    Category::STATUS_DISABLED
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Category::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'description' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email([
                        'strict' => true
                    ])
                ]
            ])
            ->add('enabled', ChoiceType::class, [
                'description' => 'Is enabled',
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'choices' => [0, 1],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => User::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

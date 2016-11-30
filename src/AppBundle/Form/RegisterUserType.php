<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserType extends AbstractType
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
            ->add('plainPassword', PasswordType::class, [
                'description' => 'Password (Length: min = 6, max = 32)',
                'label' => 'user.password',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type([
                        'type' => 'string',
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'max' => 32
                    ])
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

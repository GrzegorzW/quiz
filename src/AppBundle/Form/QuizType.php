<?php

namespace AppBundle\Form;

use AppBundle\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);

        $builder
            ->add('content', TextType::class, [
                'description' => 'Text content',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('correctAnswer', TextType::class, [
                'description' => 'Correct answer',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('answers', CollectionType::class, [
                'description' => 'Incorrect answers',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('imageFile', FileType::class, [
                'description' => 'Image file',
                'constraints' => [
                    new NotBlank(),
                    new File([
                        'maxSize' => '8M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/gif'
                        ]
                    ])
                ]
            ]);
    }

    public function onPreSetData(FormEvent $event)
    {
        $question = $event->getData();

        if ($question instanceof Question && $question->getId() !== null) {
            $form = $event->getForm();
            $form->add('status', ChoiceType::class, [
                'description' => 'Status',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    Question::STATUS_ENABLED => Question::STATUS_ENABLED,
                    Question::STATUS_DISABLED => Question::STATUS_DISABLED
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Question::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

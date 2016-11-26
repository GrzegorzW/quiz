<?php

namespace AppBundle\Form;

use AppBundle\Entity\Question;
use AppBundle\Validator\Constraints\ArrayCollectionType;
use AppBundle\Validator\Constraints\NotEmptyArrayCollection;
use AppBundle\Validator\Constraints\UniqueQuestionAnswers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextType::class, [
                'description' => 'Text content',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('correctAnswer', AnswerType::class, [
                'description' => 'Correct answer',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('answers', CollectionType::class, [
                'description' => 'Incorrect answers',
                'entry_type' => AnswerType::class,
                'by_reference' => false,
                'allow_add' => true,
                'constraints' => [
                    new NotEmptyArrayCollection(),
                    new UniqueQuestionAnswers()
                ]
            ])
            ->add('image', ImageType::class, [
                'description' => 'Image',
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('status', ChoiceType::class, [
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Question::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}

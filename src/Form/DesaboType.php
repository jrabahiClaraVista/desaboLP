<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DesaboType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choice', ChoiceType::class, array(
                'choices' => array(
                    'Je ne suis plus intéressé' => 'r1',
                    'Les messages sont trop fréquents' => 'r2',
                    'Je n\'arrive pas à lire les messages' => 'r3',
                    'Je n\'ai jamais demandé à être inscrit' => 'r4',
                    'Autre' => 'r5',
                ),
                'expanded' => true
                ))
            ->add('autre', TextType::class, array('required' => false))
            ->add('submit', SubmitType::class, array('label' => 'Je me désinscris'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
        ]);
    }
}

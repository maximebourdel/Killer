<?php

namespace Games\KillerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormInterface;
class PlayerEliminationType extends AbstractType
{
    
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
            
         ->add('password', 'text', array(
            'data' => null,
        ))
         ->add('save', 'submit', array('label' => 'Valider'))
      ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(
            array(
                'data_class' => 'Games\KillerBundle\Entity\Player',
            )
    );
    
  }

  public function getName()
  {
    return 'games_killerbundle_eliminationPlayer';
  }
  
  
}
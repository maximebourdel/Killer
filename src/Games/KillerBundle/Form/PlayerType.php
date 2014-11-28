<?php

namespace Games\KillerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlayerType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
            
        ->add('save',     'submit', array('label' => 'Je veux participer'))
      ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Games\KillerBundle\Entity\Player'
    ));
  }

  public function getName()
  {
    return 'games_killerbundle_player';
  }
}
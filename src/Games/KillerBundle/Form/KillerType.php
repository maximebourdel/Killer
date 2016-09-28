<?php

namespace Games\KillerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KillerType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name',     'text')
      ->add('adresse',  'text', array('read_only' => true))
      ->add('latitude',  'hidden')
      ->add('longitude',  'hidden')
      ->add('save',     'submit')
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Games\KillerBundle\Entity\Killer'
    ));
  }

  public function getName()
  {
    return 'games_killerbundle_killer';
  }
}

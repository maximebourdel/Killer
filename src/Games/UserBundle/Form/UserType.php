<?php

namespace Games\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('username',     'text')
      ->add('plainpassword','password')
      ->add('email',        'email')
      ->add('save',         'submit')
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Games\UserBundle\Entity\User'
    ));
  }

  public function getName()
  {
    return 'games_userbundle_user';
  }
}
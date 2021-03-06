<?php

namespace userBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nick')
            ->add('firstName')
            ->add('lastName')
            ->add('email', 'email')
            ->add('password', 'password')
            ->add('role', 'choice', array('choices' => array('ROLE_USER' => 'User','ROLE_ADMIN' => 'Administrator')))
            ->add('isActive', 'checkbox')
            ->add('save', 'submit', array('label' => 'Save user'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'userBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'userbundle_user';
    }
}

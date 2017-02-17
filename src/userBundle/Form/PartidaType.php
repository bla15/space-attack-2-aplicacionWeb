<?php

namespace userBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartidaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombrePiloto')
            ->add('raza')
            ->add('user', 'entity', array(
                'class'         => 'userBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :only')
                        ->setParameter('only', 'ROLE_USER');
                }
                    ->add('ultimoPlaneta')
                    ->add('disparos')
                    ->add('deads')
                    ->add('score')
                    ->add('life')
                    ->add('status')
                    ->add('createdAt')
                    ->add('updatedAt')
                    ->add('user')
                ;
            }

                /**
             * @param OptionsResolverInterface $resolver
             */
                public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
                    $resolver->setDefaults(array(
                        'data_class' => 'userBundle\Entity\Partida',
                    ));
                }

                /**
             * @return string
             */
                public function getName()
    {
                    return 'partida';
                }
            }

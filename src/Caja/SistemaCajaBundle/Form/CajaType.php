<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CajaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('ip')
            ->add('cajero'
//            , 'entity',
//            array(
//                'class' => 'UsuarioBundle:Usuario',
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('u');
//                },
//            )
        )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Caja'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_cajatype';
    }
}

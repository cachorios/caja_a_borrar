<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HabilitacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('desde')
            //->add('hasta', 'text', array('empty_value' => "Seleccione la fecha"))
            ->add('hasta','text')
            ->add('caja')
            ->add('usuario')
            ->add('puesto')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Habilitacion'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_habilitacion';
    }
}

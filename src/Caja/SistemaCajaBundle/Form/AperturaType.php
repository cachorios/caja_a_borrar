<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AperturaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(null == $options['data']->getId()) {
            $builder->add('fecha');
        }else{
            $builder->add('fecha',null,array("disabled" =>true));
        }
        $builder->add('importe_inicial','number');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Apertura'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_aperturatype';
    }
}
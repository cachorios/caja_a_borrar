<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AperturaCierreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fecha',null,array("disabled" =>true));
        $builder->add('importe_inicial','number',array("disabled" =>true));
        $builder->add('comprobante_cantidad','number',array("disabled" =>true));
        $builder->add('comprobante_anulado','number',array("disabled" =>true));
        $builder->add('importe_cobro','number',array("disabled" =>true));
        $builder->add('importe_anulado','number',array("disabled" =>true));
        $builder->add('fecha_cierre', null,array("disabled" =>true));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Apertura'
        ));
    }

    public function getName()
    {
        //return 'caja_sistemacajabundle_aperturacierretype';
        return 'Datos';
    }
}

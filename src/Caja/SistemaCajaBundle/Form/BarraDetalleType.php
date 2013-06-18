<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BarraDetalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('posicion','number',array('label' => 'Pos.'))
            ->add('longitud','number',array('label' => 'Long.'))
            ->add('descripcion')
            ->add('tabla','parametro_choice',array(
                        'tabla' => 0,'attr' =>array('data-choice' =>'barra-detalle',)))
            ->add("ver",    "checkbox", array("required"=>false))
            ->add("seccion","radio",    array("required"=>false, "attr" => array("class" => "pos_check_seccion")))
            ->add("comp",   "radio",    array("required"=>false, "attr" => array("class" => "pos_check_comp")))

            /*->add('estado','choice',array(
                'choices' => array(
                        'empty_value' => "Seleccione",
                        1 => 'Ver',
                        2 => 'Comprobante')
            ))*/
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\BarraDetalle'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_barradetalletype';
    }
}

<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CodigoBarraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('empresa')
            ->add('longitud')
            ->add('identificador')
            ->add('valor')
            ->add('conReferencia')
            ->add('observacion',null,array('attr' =>array("style" => "width: 96%;")))
            ->add('posiciones','collection', array('block_name' => "Block name",
                'type' => new BarraDetalleType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                ))
            ->add('vtos_importes','collection', array('label' => " Vencimientos e Importes ",
            'type' => new VtoImporteCodBarraType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,

        ))
        ;

        //"help_block", "help_inline", "help_label",
//        ->add('posiciones','collection', array(
//            'type' => new BarraDetalleType(),
//            'allow_add' => true,
//            'allow_delete' => true,
//            'prototype' => true,
//            'widget_add_btn' => array('label' => 'Nueva Posicion', 'attr' => array('class' => 'btn btn-small')),
//            'options' => array( // options for collection fields
//                'widget_remove_btn' => array(
//                    'label' => 'Quitar',
//                    'attr' => array('class' => 'btn btn-small')
//                ),
//                'attr' => array('class' => 'span3'),
//                'widget_addon' => array(
//                    'type' => 'prepend',
//                    'text' => '@',),
//                'widget_control_group' => true,))
//    )

//            ->add('posiciones','collection',array(
//                'type' => new BarraDetalleType(),
//                'allow_add' => true,
//                'by_reference' => false,
//            ) );

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\CodigoBarra'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_codigobarratype';
    }
}

<?php
namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 21/01/13
 * Time: 20:32
 * To change this template use File | Settings | File Templates.
 */
class LoteDetalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder

		->add('comprobante','text',array('attr' => array('class' =>'registro_comprobante')))
        ->add('referencia',"text",array('attr' => array('class' =>'registro_referencia')))
        ->add('fecha',"text",array('attr' => array('class' =>'registro_fecha')))
        ->add('importe','text',array('attr' => array('class' =>'importe')))
		->add('codigo_barra','hidden')
        ->add('seccion','hidden')


    ;
}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(
        'data_class' => 'Caja\SistemaCajaBundle\Entity\LoteDetalle'
    ));
}

    public function getName()
{
    return 'caja_sistemacajabundle_lotedetalletype';
}
}

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
class LotePagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder

		->add('tipo_pago')
        ->add('importe','money',array('attr' => array('class' =>'importe')))

    ;
}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(
        'data_class' => 'Caja\SistemaCajaBundle\Entity\LotePago'
    ));
}

    public function getName()
{
    return 'caja_sistemacajabundle_lotepagotype';
}
}

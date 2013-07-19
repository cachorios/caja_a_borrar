<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cacho
 * Date: 27/06/13
 * Time: 10:28
 * To change this template use File | Settings | File Templates.
 */

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;


class ResponsableFilterType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion', 'filter_text')
            ->add('email', 'filter_text')
            ->add('detalle', 'filter_boolean')
            ->add('resumen', 'filter_boolean')
            ->add('activo', 'filter_boolean')
        ;

        $listener = function(FormEvent $event)
        {
            // Is data empty?
            foreach ($event->getData() as $data) {
                if(is_array($data)) {
                    foreach ($data as $subData) {
                        if(!empty($subData)) return;
                    }
                }
                else {
                    if(!empty($data)) return;
                }
            }

            $event->getForm()->addError(new FormError('Filter empty'));
        };
        $builder->addEventListener(FormEvents::POST_BIND, $listener);
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_responsablefiltertype';
    }

}
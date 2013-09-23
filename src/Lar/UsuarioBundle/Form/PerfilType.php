<?php

namespace Lar\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PerfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nombre' )
            ->add('apellido')
            ->add('username')
            ->add('dni')
            ->add('email');
        if(null == $options['data']->getId()) {
            $builder->add('password','repeated', array(
                'type'              => 'password',
                'invalid_message'   => 'Las dos contraseñas deben coincidir',
                'options'           => array('label' => 'Contraseña'))) ;
        }else{
            $builder->add('password','repeated', array(
                'type'              => 'password',
                'invalid_message'   => 'Las dos contraseñas deben coincidir',
                'required'          => false,
                'options'           => array(
                        'label' => 'Contraseña',
                        'help_block' => "Deje vacio si no cambiará la contraseña")
            )) ;

        }

        $builder
            ->add('direccion')
            ->add('permite_email',null,array(
                    'required'   => false,
                    //'help_inline' => 'Help in line',
                    'help_block' => 'El usuario recibirá informacion.'
            ))
            ->add('foto','hidden',array(
                'larutils_enabled' => true,
                'div_img' => 'foto',
                'larutils' => array(
                    'uploader'  =>'lar_utils_upload',
                    'folderUpload' => 'uploads/users',
                    'queueID' => 'fotoUp',
                    'buttonText' => 'Cambiar Fotografia',
                    'width'     => '100%',
                    'height'    => 28),
                'read_only' => true
            ))

            ->add('fecha_alta','datetime', array(
                'widget'    => 'single_text',
                'format' => 'dd-M-yyyy H:m',
                'read_only' => true))

            ->add('fecha_nacimiento','birthday')
		;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lar\UsuarioBundle\Entity\Usuario'
        ));
    }

    public function getName()
    {
        return 'lar_usuariobundle_usuariotype';
    }
}

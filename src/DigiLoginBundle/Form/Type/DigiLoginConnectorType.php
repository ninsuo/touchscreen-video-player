<?php

namespace DigiLoginBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DigiLoginConnectorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('login', Type\TextType::class, [
               'label'          => 'digilogin.manage.form.login',
               'error_bubbling' => true,
           ])
           ->add('pin', Type\PasswordType::class, [
               'label'          => 'digilogin.manage.form.pin',
               'error_bubbling' => true,
           ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DigiLoginBundle\Entity\DigiLogin'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'digilogin_connector';
    }
}

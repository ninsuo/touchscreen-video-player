<?php

namespace DigiLoginBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DigiLoginManagerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           ->add('login', Type\TextType::class, [
               'label'       => 'digilogin.manage.form.login',
           ])
           ->add('pin', Type\PasswordType::class, [
               'label'       => 'digilogin.manage.form.pin',
           ])
           ->add('maxTries', Type\NumberType::class, [
               'label'       => 'digilogin.manage.form.max_tries',
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => 'base.crud.action.save',
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
        return 'digilogin_manager';
    }
}

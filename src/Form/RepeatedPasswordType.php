<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeatedPasswordType extends AbstractType
{
    public function getParent(): string
    {
        return RepeatedType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe ne correspondent pas',
            'first_options' => [
                'label' => 'Mot de passe',
            ],
            'second_options' => [
                'label' => 'Confirmer le mot de passe',
            ],
            'required' => false,
        ]);
    }
}

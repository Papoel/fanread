<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'       => 'Prénom',
                'required'    => false,
                'constraints' => new Length(max: 50),
            ])
            ->add('lastname', TextType::class, [
                'label'       => 'Nom',
                'required'    => false,
                'constraints' => new Length(max: 50),
            ])
            ->add('pseudo', TextType::class, [
                'label'       => 'Pseudo',
                'required'    => false,
                'constraints' => new Length(max: 50),
                'help'        => 'Affiché à la place de votre nom si activé ci-dessous.',
            ])
            ->add('isPseudoDisplayed', CheckboxType::class, [
                'label'    => 'Afficher mon pseudo dans l\'application',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}

<?php

declare(strict_types=1);

namespace App\Form\Book;

use App\Entity\Book;
use App\Enum\Book\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ISBN en premier (recherche rapide — fonctionnalité step-06)
            ->add('isbn', TextType::class, [
                'label'    => 'Recherche rapide par ISBN',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Ex : 9782070612758',
                    'maxlength'   => 20,
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr'  => ['placeholder' => 'Titre du livre'],
                'constraints' => [
                    new NotBlank(message: 'Le titre est obligatoire.'),
                    new Length(max: 100),
                ],
            ])
            ->add('author', TextType::class, [
                'label'    => 'Auteur',
                'required' => false,
                'attr'     => ['placeholder' => "Nom de l'auteur"],
                'constraints' => [new Length(max: 50)],
            ])
            ->add('totalPages', IntegerType::class, [
                'label'    => 'Nombre de pages',
                'required' => false,
                'attr'     => ['placeholder' => 'Ex : 350', 'min' => 1],
                'constraints' => [new Positive(message: 'Le nombre de pages doit être positif.')],
            ])
            ->add('category', EnumType::class, [
                'class'        => Category::class,
                'label'        => 'Catégorie',
                'required'     => false,
                'placeholder'  => 'Choisir une catégorie',
                'choice_label' => fn(Category $c) => $c->label(),
            ])
            ->add('coverUrl', UrlType::class, [
                'label'       => 'URL de la couverture',
                'required'    => false,
                'default_protocol' => 'https',
                'attr'        => ['placeholder' => 'https://...'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'help'     => 'Laissez ce champ vide pour récupérer automatiquement la description de Google Books lors de la recherche par ISBN. Si vous écrivez votre propre description, elle sera conservée ; effacez son contenu pour rappatrier celle de l\'API.',
                'attr'     => [
                    'placeholder' => 'Description du livre (ou laissez vide pour utiliser celle de Google Books)',
                    'rows'        => 5,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Book::class]);
    }
}

<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez le trick en quelques mots',
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => 'name',
                'invalid_message' => "Cette catégorie n'est pas valide",
            ])
            ->add('mainImage', ImageType::class, [
                'label' => 'Image de couverture',
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                // $mainImage = $data->getMainImage();

                // if (null === $mainImage->getFile()) {
                //     $data->removeImage($mainImage);
                // }

                foreach ($data->getVideos() as $video) {
                    if ('' === $video->getUrl()) {
                        $data->removeVideo($video);
                    }
                }
                foreach ($data->getImages() as $image) {
                    if (null === $image->getFile() && null === $image->getName()) {
                        $data->removeImage($image);
                    }
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}

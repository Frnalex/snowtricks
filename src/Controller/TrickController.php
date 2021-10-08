<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    /**
     * @Route("/{slug}", name="trick_show")
     */
    public function show(Trick $trick)
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/trick/add", name="trick_add")
     */
    public function add(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $builder = $factory->createBuilder(FormType::class, null, [
            'data_class' => Trick::class,
        ]);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Tapez le nom du trick',
                ],
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
            ])
        ;

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $trick = $form->getData();
            $trick->setSlug($slugger->slug($trick->getName())->lower());

            $em->persist($trick);
            $em->flush();
            dd($trick);
        }

        $formView = $form->createView();

        return $this->render('trick/add.html.twig', [
            'formView' => $formView,
        ]);
    }
}

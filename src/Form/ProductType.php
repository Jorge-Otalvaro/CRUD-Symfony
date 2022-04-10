<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nombre'
            ])
            ->add('code', TextType::class, [
                'required' => true,
                'label' => 'Código'
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'label' => 'Descripción'
            ])
            ->add('brand', TextType::class, [
                'required' => true,
                'label' => 'Marca'
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'label' => 'Valor'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Seleccione una categoría',
                'label' => 'Categoría',
                'required' => true,
                'multiple' => false,
                'attr' => array('class' => 'form-control')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

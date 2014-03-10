<?php
namespace Sdz\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of ArticleType
 *
 * @author david
 */
class ArticleType extends AbstractType{
    
    public function getName() 
    {
        return 'sdz_blogbundle_articletype';
    }

    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
            ->add('date',        'date')
            ->add('titre',       'text')
            ->add('contenu',     'textarea')
            ->add('auteur',      'text')
            ->add('publication', 'checkbox', array('required' => false))
            ->add('image', new ImageType())
            ->add('categories', 'entity', array(
                                'class'    => 'SdzBlogBundle:Categorie',
                                'property' => 'nom',
                                'multiple' => true)
                  )
          ;
    }
    
    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'Sdz\BlogBundle\Entity\Article'
                ));
    }


}

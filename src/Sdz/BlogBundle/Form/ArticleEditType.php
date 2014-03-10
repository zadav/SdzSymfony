<?php
namespace Sdz\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of ArticleType
 *
 * @author david
 */
class ArticleEditType extends AbstractType{
    
    public function getName() 
    {
        return 'sdz_blogbundle_articleedittype';
    }

    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        parent::buildForm($builder, $options);
        $builder->remove('date',        'date');
          ;
    }
    
    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'data_class' => 'Sdz\BlogBundle\Entity\Article'
                ));
    }


}

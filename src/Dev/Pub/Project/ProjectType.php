<?php
namespace Dev\Pub\Project;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Dev\Pub\Keyword\KeywordType;



class ProjectType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
	        ->add('name', 'text')
	        
	        ->add('country', 'country')
	        ->add('language', 'language')
	        ->add('search_engine', 'choice', array(
	            'choices' => array(1 => 'google.es', 2 => 'google.fr'),
	            'expanded' => true,
	        ))
	        ->add('start_date', 'datetime')
	        ->add('end_date', 'datetime')
            ->add('keywords', 'collection', array(
                'type' => new KeywordType(),
                'allow_add' => true,
            ))
	        ->add('submit', 'submit')
	    ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dev\Pub\Entity\Project',
        ));
    }

    public function getName()
    {
        return 'dev_pub_projecttype';
    }
}
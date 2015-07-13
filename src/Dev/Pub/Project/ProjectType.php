<?php
namespace Dev\Pub\Form;
use Symfony\Component\Form\AbstractType;

class ProjectType extends AbstractType
{
    public function __construct()
    {
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dev\Pub\Entity\Project',
        ));
    }
}
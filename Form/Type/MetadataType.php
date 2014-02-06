<?php 
namespace Openview\TreeRepoBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MetadataType extends AbstractType {
    protected $doctrine;
    
    
    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'required' => true,
        ));
        $builder->add('filetype', 'entity', array(
                'class' => 'OpenviewTreeRepoBundle:FileType',
                'property' => 'name',
            ));
        
        $builder->add('id', 'hidden');
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Openview\TreeRepoBundle\Entity\Node',
        ));
    }
    
    
    public function getName()
    {
        return "treerepo_node_metadata";
    }
}

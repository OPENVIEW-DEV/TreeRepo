<?php 
namespace Openview\TreeRepoBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FolderType extends AbstractType {
    protected $parentid;
    
    
    public function __construct($parentid) {
        $this->parentid = $parentid;
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'required' => true,
        ));
        $builder->add('id', 'hidden');
        $builder->add('parent_id', 'hidden', array('mapped' => false, 'data'=>$this->parentid));
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Openview\TreeRepoBundle\Entity\Node',
        ));
    }
    
    
    public function getName()
    {
        return "treerepo_node_folder";
    }
}

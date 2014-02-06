<?php
namespace Openview\TreeRepoBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * File type definition
 *
 * @ORM\Table(name="treerepo_filetype")
 * @ORM\Entity
 */
class FileType {
    
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string Filetype name
     * @ORM\Column(type="string")
     */
    protected $name;
    /**
     * @var string Dynamic Document Template name
     * @ORM\Column(type="string")
     */
    protected $templateName;
    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="filetype")
     */
    protected $nodes;
    
    
    
    /*
     * Getter and setter
     */
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getTemplateName() {
        return $this->templateName;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setTemplateName($templateName) {
        $this->templateName = $templateName;
        return $this;
    }
    public function getNodes() {
        return $this->nodes;
    }

    public function addNodes($node) {
        $this->nodes[] = $node;
        return $this;
    }
    
    public function removeNodes($node) {
        $this->nodes->removeElement($node);
        return $this;
    }





    
}

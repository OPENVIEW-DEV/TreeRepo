<?php
namespace Openview\TreeRepoBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Openview\TreeRepoBundle\Entity\Node;

/**
 * Revision for a node
 *
 * @ORM\Table(name="treerepo_node_revision")
 * @ORM\Entity
 */
class NodeRevision {
    
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     * @ORM\Column(name="storeditem_id", type="string")
     */
    protected $storedItemId;
    /**
     * Node referring to this revision
     * 
     * @var integer
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="revisionss")
     * @ORM\JoinColumn(name="revision_id", referencedColumnName="id")
     */
    protected $node;
    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    
    public function getStoredItemId() {
        return $this->storedItemId;
    }
    public function setStoredItemId($storedItemId) {
        $this->storedItemId = $storedItemId;
        return $this;
    }
    public function getId() {
        return $this->id;
    }
    public function getNode() {
        return $this->node;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    public function setNode($node) {
        $this->node = $node;
        return $this;
    }
    public function getCreated() {
        return $this->created;
    }


}

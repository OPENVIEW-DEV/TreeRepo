<?php
namespace Openview\TreeRepoBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Openview\TreeRepoBundle\Entity\NodeRevision;

/**
 * Class representing a node in the repository tree. Can be a Directory or a file,
 * and contains all its metadata.
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="treerepo_node")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 */
class Node {
    /* node types consts */
    const TYPE_FILE = 0;
    const TYPE_FOLDER = 1;
    
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;
    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;
    /**
     * @var string
     * @ORM\Column(name="filename", type="string", nullable=true)
     */
    protected $fileName;
    /**
     * @var integer
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    protected $type;
    /**
     *
     * @var array
     * @ORM\Column(name="revision_id", type="integer", nullable=true)
     * @ORM\OneToMany(targetEntity="NodeRevision", mappedBy="node")
     */
    protected $revisions;
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer", nullable=true)
     */
    protected $lft;
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer", nullable=true)
     */
    protected $lvl;
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer", nullable=true)
     */
    protected $rgt;
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    protected $root;
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;
    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;
    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;
    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;
    
    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getFileName() {
        return $this->fileName;
    }
    public function getType() {
        return $this->type;
    }
    public function getLft() {
        return $this->lft;
    }
    public function getLvl() {
        return $this->lvl;
    }
    public function getRgt() {
        return $this->rgt;
    }
    public function getRoot() {
        return $this->root;
    }
    public function getParent() {
        return $this->parent;
    }
    public function getChildren() {
        return $this->children;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    public function setFileName($fileName) {
        $this->fileName = $fileName;
        return $this;
    }
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    public function setLft($lft) {
        $this->lft = $lft;
        return $this;
    }
    public function setLvl($lvl) {
        $this->lvl = $lvl;
        return $this;
    }
    public function setRgt($rgt) {
        $this->rgt = $rgt;
        return $this;
    }
    public function setRoot($root) {
        $this->root = $root;
        return $this;
    }
    public function setParent($parent) {
        $this->parent = $parent;
        return $this;
    }
    public function setChildren($children) {
        $this->children = $children;
        return $this;
    }
    public function getCreated() {
        return $this->created;
    }
    public function getUpdated() {
        return $this->updated;
    }
    
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->revisions = new ArrayCollection();
    }


}

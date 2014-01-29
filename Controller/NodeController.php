<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Openview\TreeRepoBundle\Form\Type\FolderType;
use Openview\TreeRepoBundle\Entity\Node;

/**
 * Manages repository structure: display, tree navigation, upload, ...
 * 
 */
class NodeController extends Controller
{
    /**
     * Show "directory" index.
     */
    public function indexAction($nodeid)
    {
        $folders = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')
                ->findBy(
                        array(
                            'type'=>Node::TYPE_FOLDER,
                            'parent'=>$nodeid
                            ),
                        array('name'=>'ASC')
                );
        return $this->render('OpenviewTreeRepoBundle:Node:index.html.twig', array('folders'=>$folders));
    }
    
    
    
    /**
     * Create a new folder
     */
    public function newFolderAction(Request $request, $nodeid)
    {
        $folder = new Node();
        $parentFolder = null;
        $form = $this->createForm(new FolderType($nodeid), $folder);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $parentid = $form->get('parent_id')->getData();
                if ($parentid !== null) {
                    $parentFolder = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')->find($parentid);
                    //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($parentFolder); exit;
                }
                $folder->setType(Node::TYPE_FOLDER);
                $folder->setParent($parentFolder);
                //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($folder); exit;
                $em = $this->getDoctrine()->getManager();
                $em->persist($folder);
                $em->flush();
                return $this->redirect($this->generateUrl('openview_treerepo_node_index', array('nodeid'=>$nodeid)));
            }
        }
        
        return $this->render('OpenviewTreeRepoBundle:Node:newFolder.html.twig', array(
            'form' => $form->createView(),
            'currentFolder' => new Node(),
        ));
    }
    
    
    
    /**
     * Ritorna l'elenco della directory indicata
     * @return string The folders list in json format
     */
    public function rpcListDirAction($nodeid) {
        $folders = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')
                ->findBy(
                        array(
                            'type'=>Node::TYPE_FOLDER,
                            'parent'=>$nodeid
                        ), 
                        array('name'=>'ASC')
                );
        if ($folders !== false) {
            $foldersArray = array('status'=>'OK', 'data'=>array());
            foreach ($folders as $folder) {
                $foldersArray['data'][] = array(
                    'id'=>$folder->getId(),
                    'name'=>$folder->getName(),
                    'type'=>'folder',
                    'additionalParameters'=>array(
                        'id'=>$folder->getId(),
                        'children'=>true,
                        'itemSelected'=>false,
                    )
                );
            }
            $s = json_encode($foldersArray);
        } else {
            $s = '"status":"KO","data":[]}';
        }
        return new Response($s);
    }
    
    
    /**
     * Ritorna l'elenco dei file aventi come padre il nodo indicato
     * @return string The files list in json format
     */
    public function rpcListFileAction($nodeid) {
        $files = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')
                ->findBy(
                        array(
                            'type'=>Node::TYPE_FILE,
                            'parent'=>$nodeid
                        ), 
                        array('name'=>'ASC')
                );
        if ($files !== false) {
            $filesArray = array('status'=>'OK', 'data'=>array());
            foreach ($files as $file) {
                $filesArray['data'][] = array(
                    'id'=>$folder->getId(),
                    'name'=>$folder->getName(),
                    'type'=>'file',
                    'additionalParameters'=>array(
                        'id'=>$folder->getId(),
                        'children'=>true,
                        'itemSelected'=>false,
                    )
                );
            }
            $s = json_encode($filesArray);
        } else {
            $s = '"status":"KO","data":[]}';
        }
        return new Response($s);
    }
}

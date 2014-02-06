<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Openview\TreeRepoBundle\Entity\Node;
use Openview\TreeRepoBundle\Form\Type\MetadataType;
use Openview\TreeRepoBundle\Entity\FileType;

/**
 * Manages metadata for a specific Node
 * 
 */
class MetadataController extends Controller
{
    /**
     * Permette di modificare i metadata tramite un wizard
     */
    public function editWizardAction(Request $request, $nodeid)
    {
        // load node to be edited
        $node = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')
                ->find($nodeid);
        // build edit form
        $form = $this->createForm(new MetadataType($this->getDoctrine()), $node);
        
        // if receiving the form
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            //
        }
        
        return $this->render('OpenviewTreeRepoBundle:Metadata:editWizard.html.twig', array(
            'node'=>$node,
            'form' => $form->createView(),
        ));
    }
    
    
    
    /**
     * Imposta il tipo di file per un determinato nodo
     */
    public function rpcSetFiletypeAction($nodeid, $filetypeid) {
        // istanzia nodo e filetype
        $node = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')
                ->find($nodeid);
        $filetype = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:FileType')
                ->find($filetypeid);
        
        // modifica valore
        if (($node !== false) && ($filetype !== false)) {
            try {
                // modifica e salva nodo
                $node->setFileType($filetype);
                $em = $this->getDoctrine()->getManager();
                $em->persist($node);
                $em->flush();
                return new Response('Set ' . $filetypeid . ' to node ' . $nodeid, 200);
            } catch (\Exception $e) {
                return new Response('Exception', 500);
            }
        }
        // altrimenti ritorna errore
        return new Response('Ko', 500);
    }
    
    
    
    /**
     * Ritorna l'HTML che costituisce l'elenco dei metedata di un nodo
     * 
     * @param int $nodeid
     * @return string Codice HTML
     */
    public function rpcGetMetadataAction($nodeid) {
        // istanzia nodo e filetype
        $node = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')
                ->find($nodeid);
        if ($node !== false) {
            if ($node->getMetadata() !== null) {
                // legge campi da DynDoc in un array
                // ritorna template che mostra l'elenco (privo di header eccetera)
            } else {
                // ritorna template con elenco vuoto
            }
            // temporaneo: ritorna il template con elenco di test
            return $this->render('OpenviewTreeRepoBundle:Metadata:_metadataProperties.html.twig');
        }
        // altrimenti ritorna errore
        return new Response('Ko', 500);
    }
    
    
    
    public function provaAction(Request $request, $nodeid) {
        $items = $this->getDoctrine()->getManager()->getRepository('OpenviewTreeRepoBundle:FileType')
                ->findAll();
        
        $filetype = new \Openview\TreeRepoBundle\Entity\FileType();
        $form = $this->createForm(new \Openview\TreeRepoBundle\Form\Type\FileTypeType(), $filetype);
        return $this->render('OpenviewTreeRepoBundle:Metadata:prova.html.twig', array(
            'form' => $form->createView(),
            'items' => $items,
        ));
        
    }
    
    

}

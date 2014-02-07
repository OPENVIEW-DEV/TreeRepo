<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Openview\TreeRepoBundle\Entity\Node;
use Openview\TreeRepoBundle\Form\Type\MetadataType;
use Openview\TreeRepoBundle\Entity\FileType;
use Openview\DynamicDocumentBundle\Document\DynamicDocument;
use Openview\DynamicDocumentBundle\Document\DynamicDocumentStructureTemplate;

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
     * Avvia la creazione o modifica del dynamic document contenente i metadata di un nodo
     * @param type $nodeid
     */
    public function editAction($nodeid) {
        try {
            // legge nodo
            $node = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')->find($nodeid);
            // se non esiste il documento, lo si crea
            if ($node->getMetadata() === null) {
                // cerca template id
                $tplName = $node->getFiletype()->getTemplateName();
                $template = $this->get('doctrine_mongodb')
                        ->getRepository('OpenviewDynamicDocumentBundle:DynamicDocumentStructureTemplate')
                        ->findOneBy(array('name'=>$tplName));
                // se il template esiste
                if ($template) {
                    //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($template); exit;
                    return $this->redirect($this->generateUrl('openview_dyndoc_formnew', array('templateid'=>$template->getId())));
                }
                // se il template non esiste
                else {
                    $this->get('session')->getFlashBag()->add(
                        'info',
                        'Template not found: ' . $tplName);
                }
            }
            // se invece esiste, lo si modifica
            else {
                return $this->redirect($this->generateUrl('openview_dyndoc_formedit', array('documentid'=>$node->getMetadata())));
            }
        } catch (\Exception $e) {
            //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($e); exit;
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->get('translator')->trans('msg.editko') . ': ' . $e->getMessage());
        }
        return $this->redirect($this->generateUrl('openview_treerepo_metadata_editwizard', array('nodeid'=>$nodeid)));
    }
    
    
    
    /**
     * Elimina il dynamic document con i metadata di un nodo
     * @param type $nodeid
     */
    public function deleteAction($nodeid) {
        try {
            // elimina riferimento ai metadati dal nodo
            $node = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')->find($nodeid);
            $metadataDoc = $node->getMetadata();
            $node->setMetadata(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($node);
            $em->flush();
            // cancella dyndoc con metadati
            $dyndoc = $this->get('doctrine_mongodb')->getRepository('OpenviewDynamicDocumentBundle:DynamicDocument')->find($metadataDoc);
            // segnala eliminazione riuscita
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('msg.deleteok'));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->get('translator')->trans('msg.deleteko'));
        }
        return $this->redirect($this->generateUrl('openview_treerepo_metadata_editwizard', array('nodeid'=>$nodeid)));
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
            $items = array();
            if ($node->getMetadata() !== null) {
                // legge campi da DynDoc in un array
                // ritorna template che mostra l'elenco (privo di header eccetera)
                return $this->render('OpenviewTreeRepoBundle:Metadata:_metadataProperties.html.twig', array('items'=>$items));
            } else {
                // ritorna template con elenco vuoto
                return $this->render('OpenviewTreeRepoBundle:Metadata:_metadataProperties.html.twig', array('items'=>$items));
            }
            // temporaneo: ritorna il template con elenco di test
            return $this->render('OpenviewTreeRepoBundle:Metadata:_metadataProperties.html.twig');
        }
        // altrimenti ritorna errore
        return new Response('Ko', 500);
    }
    
    

}

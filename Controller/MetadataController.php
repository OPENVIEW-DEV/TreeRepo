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
use Symfony\Bundle\FrameworkBundle\Client;

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
                if ($node->getFiletype() !== null) {
                    $tplName = $node->getFiletype()->getTemplateName();
                    $template = $this->get('doctrine_mongodb')
                            ->getRepository('OpenviewDynamicDocumentBundle:DynamicDocumentStructureTemplate')
                            ->findOneBy(array('name'=>$tplName));
                    // se il template esiste
                    if ($template) {
                        //esempio: http://ov_intranet.nicola-dev.openview.lan/app_dev.php/repo/node/metadatawizard/edit/28
                        //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($template); exit;
                        // crea documento vuoto da template, via REST
                        $url = $this->generateUrl(
                                    'api_1_dyndoc_get_newdocbytemplate', 
                                    array('templateid'=>$template->getId(), '_format'=>'json'),
                                    true
                                );
                        //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($url); exit;
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($curl); exit;
                        $result = curl_exec($curl);
                        //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($result); //exit;
                        if ($result !== false) {
                            // associa documento a nodo
                            $documentArray = json_decode($result, true);
                            //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($documentArray); exit;
                            if ((is_array($documentArray)) && (array_key_exists('id', $documentArray))) {
                                $docId = $documentArray['id'];
                                $node->setMetadata($docId);
                                //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($node); exit;
                                $em = $this->get('doctrine')->getManager();
                                $em->persist($node);
                                $em->flush();
                                //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($node); exit;
                                // redirect all'editing del documento
                                return $this->redirect($this->generateUrl('openview_dyndoc_formedit', array('documentid'=>$node->getMetadata())));
                            }
                        }
                    }
                    // se il template non esiste
                    else {
                        $this->get('session')->getFlashBag()->add(
                            'info',
                            'Template not found: ' . $tplName);
                    }
                } else {
                    $this->get('session')->getFlashBag()->add(
                            'info',
                            'Il documento non ha un tipo assegnato.');
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
        exit;
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
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->remove($dyndoc);
            $dm->flush();
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
        $node = $this->getDoctrine()
                ->getRepository('OpenviewTreeRepoBundle:Node')
                ->find($nodeid);
        if ($node !== false) {
            $items = array();
            if ($node->getMetadata() !== null) {
                // istanzia il dynamic document
                $document = $this->get('doctrine_mongodb')
                        ->getRepository('OpenviewDynamicDocumentBundle:DynamicDocument')
                        ->findOneById($node->getMetadata());
                $fields = $document->getStructure()->getFields();
                foreach ($fields as $field) {
                    //echo "<pre>"; \Doctrine\Common\Util\Debug::dump($field);
                    $items[] = array(
                        'label'=>$field->getLabel(),
                        'type'=>$field->getFieldType(),
                        'value'=>$field->getValueForTest(),
                        );
                }
                /*echo "<pre>"; \Doctrine\Common\Util\Debug::dump($items, 5);
                exit;
                echo "<pre>"; \Doctrine\Common\Util\Debug::dump($document, 5); exit;*/
                // legge l'array dei campi
            }
            return $this->render('OpenviewTreeRepoBundle:Metadata:_metadataProperties.html.twig', array('items'=>$items));
        }
        // altrimenti ritorna errore
        return new Response('Ko', 500);
    }
    
    
    
    /**
     * Ritorna l'ID del nodo avente i metadata nel dyndocid specificato
     * Quando avrò passato le opzioni al dyndoc potrò eliminare questa azione, insieme con la
     * sua chiamata in show.html.twig del DynDoc
     * 
     * @param type $dyndocid
     */
    public function renderNodeIdFromDynDocIdAction($dyndocid) 
    {
        $node = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')
                ->findOneBy(array('metadata'=>$dyndocid));
        if (!$node) {
            return new Response('');
        } else {
            return new Response($node->getId());
        }
    }
    
    

}

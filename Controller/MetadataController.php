<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Openview\TreeRepoBundle\Entity\Node;
use Openview\TreeRepoBundle\Form\Type\MetadataType;

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
        $form = $this->createForm(new MetadataType(), $node);
        
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
    
    

}

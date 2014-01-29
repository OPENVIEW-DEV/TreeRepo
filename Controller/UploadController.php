<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Openview\TreeRepoBundle\Document\StoredItem;
use Openview\TreeRepoBundle\Entity\Node;

/**
 * to upload documents in the repository
 */
class UploadController extends Controller
{
    public function uploadAction(Request $request)
    {
        $menu = new Node();
        $menu->setName('Menu');
        $primi = new Node();
        $primi->setName('Primi piatti');
        $primi->setParent($menu);
        $spaghetti = new Node();
        $spaghetti->setName('Spaghetti alla marinara');
        $spaghetti->setParent($primi);
        $minestra = new Node();
        $minestra->setName('Minestra di vermicelli');
        $minestra->setParent($primi);
        $secondi = new Node();
        $secondi->setName('Secondi piatti');
        $secondi->setParent($menu);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($menu);
        $em->persist($primi);
        $em->persist($secondi);
        $em->persist($spaghetti);
        $em->persist($minestra);
        $em->flush();
        
        //$repo = $em->getRepository('OpenviewTreeRepoBundle:Node');
        //$childs = 
 
        return $this->render('OpenviewTreeRepoBundle:Upload:uploaded.html.twig');
    }
    
    
    public function uploadAction_backup(Request $request)
    {
        $form = $this->createFormBuilder(array())
            ->add('upload', 'file')
            ->getForm();
 
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            $data = $form->getData(); 
            $upload = $data['upload'];

            $document = new StoredItem();
            $document->setFile($upload->getPathname());
            $document->setFilename($upload->getClientOriginalName());
            $document->setMimeType($upload->getClientMimeType());

            $dm = $this->get('doctrine.odm.mongodb.document_manager');
            //$dm = $this->get('doctrine_mongodb');
            $dm->persist($document);
            $dm->flush();
 
            return $this->render('OpenviewTreeRepoBundle:Upload:uploaded.html.twig');
        }
 
        return $this->render('OpenviewTreeRepoBundle:Upload:formUpload.html.twig', array(
                'form' => $form->createView()
        ));
    }
}

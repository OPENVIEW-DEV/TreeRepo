<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Openview\TreeRepoBundle\Document\StoredItem;

/**
 * to upload documents in the repository
 */
class UploadController extends Controller
{
    public function uploadAction(Request $request)
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

<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Controller per la ricerca nel treerepo
 * 
 */
class SearchController extends Controller
{
    
    
    /**
     * Ricerca semplice
     */
    public function searchAction($terms)
    {
        // sistema che usa il Elastica. Ritorna un Elastica\ResultSet
        //$itemType = $this->get('fos_elastica.index.treerepo.metadata');
        $itemType = $this->get('fos_elastica.index.treerepo');
        $items = $itemType->search($terms);
        
        return $this->render('OpenviewTreeRepoBundle:Search:search.html.twig', array(
            'items'=>$items,
            'terms'=>$terms
        ));
    }
    
    
    
    
    /**
     * Crea i dati da visualizzare nei risultati per la ricerca di un elemento
     * 
     * @param strring $type Tipo di dato nell'indice
     * @param string $id ID elemento del tipo indicato
     * @return string Parte di pagina da scrivere
     */
    public function renderSearchDataAction($type, $id) {
        $item = null;
        $metadataArray = array();
        
        if ($type === 'node') {
            // carica elemento
            $item = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')->find($id);
            if ($item) {
                // carica metadati, se esistono
                if ($item->getMetadata() != null) {
                    $metadata = $this->get('doctrine_mongodb')
                            ->getRepository('OpenviewDynamicDocumentBundle:DynamicDocument')
                            ->find($item->getMetadata());
                    $structure = $metadata->getStructure();
                    $structureName = $structure->getName();
                    //echo '<pre>'; \Doctrine\Common\Util\Debug::dump($structure->getFields(), 5); exit;
                    foreach ($structure->getFields() as $field) {
                        //echo '<pre>'; \Doctrine\Common\Util\Debug::dump($field, 5);
                        $metadataArray = array_merge($metadataArray, array($field->getLabel() => $field->getSearchableValue()));
                    }
                    //exit;
                }
                // carica dimensione file da gridfs
            }
        }
        else if ($type === 'metadata') {
            //
        }
        else {
            //
        }
        
        $searchresult = array(
            'title'=>$item->getName(),
            'path'=>'path/to/file.txt',
            'size'=>'100Kb',
            'metadata'=>$metadataArray,
        );
        
        
        
        return $this->render('OpenviewTreeRepoBundle:Search:_searchResult.html.twig', array(
            'result'=>$searchresult
        ));
    }
    
    
    

}

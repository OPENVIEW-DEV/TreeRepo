<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Openview\TreeRepoBundle\Handler\SearchResultHandler;


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
        $srH = new SearchResultHandler($this->get('doctrine_mongodb'));
        
        if ($type === 'node') {
            // carica elemento
            $item = $this->getDoctrine()->getRepository('OpenviewTreeRepoBundle:Node')->find($id);
            if ($item) {
                // carica metadati, se esistono
                $metadataArray = $srH->getMetadata($item->getMetadata());
            }
        }
        else if ($type === 'metadata') {
            // carica elemento
            $item = $this->getDoctrine()
                    ->getRepository('OpenviewTreeRepoBundle:Node')
                    ->findOneBy(array(
                        'metadata'=>$id
                    ));
            // carica metadati
            $metadataArray = $srH->getMetadata($id);
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

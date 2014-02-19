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
        $itemType = $this->get('fos_elastica.index.treerepo.storeditem');
        $items = $itemType->search($terms);
        
        return $this->render('OpenviewTreeRepoBundle:Search:search.html.twig', array(
            'items'=>$items,
            'terms'=>$terms
        ));
    }
    
    
    

}

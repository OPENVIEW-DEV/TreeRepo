<?php
namespace Openview\TreeRepoBundle\Handler;

/**
 * Handler per i risultati di ricerca
 *
 * @author NicolaDaRold
 */
class SearchResultHandler 
{
    protected $doctrine;
    
    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }
    
    
    /**
     * Ritorna l'array che elenca i metadati di un nodo. Se non ne trova o non trova
     * il documento, ritorna un array vuoto.
     * 
     * @param string $documentId DynamicDocumentID
     */
    public function getMetadata($documentId) {
        $metadataArray = array();
        
        $metadata = $this->doctrine
                ->getRepository('OpenviewDynamicDocumentBundle:DynamicDocument')
                ->find($documentId);
        if ($metadata) {
            $structure = $metadata->getStructure();
            $structureName = $structure->getName();
            foreach ($structure->getFields() as $field) {
                $metadataArray = array_merge($metadataArray, array($field->getLabel() => $field->getSearchableValue()));
            }
        }
        
        return $metadataArray;
    }
    
    
}

<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Controller for admin operations on the treerepo
 * 
 */
class AdminController extends Controller
{
    
    
    /**
     * Pannello di amministrazione
     */
    public function indexAction()
    {
        return $this->render('OpenviewTreeRepoBundle:Admin:index.html.twig');
    }
    
    
    

}

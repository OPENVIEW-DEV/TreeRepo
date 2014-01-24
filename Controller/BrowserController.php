<?php
namespace Openview\TreeRepoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BrowserController extends Controller
{
    public function indexAction()
    {
        return $this->render('OpenviewTreeRepoBundle:Browser:index.html.twig');
    }
}

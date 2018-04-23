<?php
namespace Application\Controller;

use Application\Model\Languages;
use Application\Model\Main;
use Application\Model\Parser;
use Application\Model\Terms;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;

class AjaxController extends AbstractActionController{

   /**
    * @var ViewModel
    */
   protected $_view_model;
   protected $_sm;

   public function onDispatch(MvcEvent $e){
      $this->_sm = $e->getApplication()->getServiceManager();

      $view_model = new ViewModel();
      $view_model->setTerminal(true);
      $view_model->setTemplate('/application/ajax/blank');
      $this->_view_model = $view_model;

      parent::onDispatch($e);
   }

   public function getTermsByArticleAction(){
      $model_terms = new Terms($this->_sm);
      $article_id = $this->params()->fromPost('article_id');
      return $this->_view_model
         ->setTemplate('/application/ajax/json')
         ->setVariables([
            'data' => $model_terms->getTermsByArticleId($article_id),
         ])
      ;
   }

   public function addTermToArticleAction(){
      $article_id = $this->params()->fromPost('article_id');
      $term_id = $this->params()->fromPost('term_id');
      $model_terms = new Terms($this->_sm);
      $model_terms->addTermToArticle($article_id, $term_id);
      return $this->_view_model;
   }
   
   public function delTermFromArticleAction(){
      $a2t_id = $this->params()->fromPost('a2t_id');
      $model_terms = new Terms($this->_sm);
      $model_terms->delTermFromArticle($a2t_id);
      return $this->_view_model;
   }

   public function parserAction(){
      $sm = $this->_sm;
      $model_parser = new Parser($sm);

      $model_parser->zdorovieinfo();
      return $this->_view_model;
   }

   public function translateAction(){
      $sm = $this->_sm;
      $model_parser = new Parser($sm);

      $model_parser->translateTerms();
      return $this->_view_model;
   }
   
   
   



   public function setAAction(){
      $sm = $this->_sm;
      $model_terms = new Terms($sm);
      $model_terms->setA();
      return $this->_view_model;
   }

}

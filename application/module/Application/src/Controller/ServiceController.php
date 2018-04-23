<?php
namespace Application\Controller;

use Application\Model\Languages;
use Application\Model\Main;
use Application\Model\Terms;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Auth;
use Zend\Mvc\MvcEvent;


class ServiceController extends AbstractActionController{
   
   private $_sm;

   public function onDispatch(MvcEvent $e){
      $this->_sm = $e->getApplication()
         ->getServiceManager();
      parent::onDispatch($e);
   }

   public function logoutAction(){
      $sm = $this->_sm;
      $model_auth = new Auth($sm);
      $model_auth->logout();
      return $this->redirect()->toRoute('index', ['lang' => 'ru']);
   }

   public function robotsAction(){
      $sm = $this->_sm;
      $view = new ViewModel();

      header('Content-Type: text/plain');
      $model_languages = new Languages($sm);
      $langs = $model_languages->getAllActive();

      return $view->setTerminal(true)->setVariables([
         'langs' => array_column($langs, 'code'),
         'sections' => Main::getSections(),
      ]);
   }

   public function sitemapAction(){
      $sm = $this->_sm;
      $view = new ViewModel();
      $model_terms = new Terms($sm);
      $terms = $model_terms->getForSitemap();
      $model_languages = new Languages($sm);
      $langs = $model_languages->getAllActive();

      header('Content-Type: text/xml');
      return $view->setTerminal(true)->setVariables([
         'terms' => $terms,
         'langs' => array_column($langs, 'code'),
      ]);
   }
   
   public function sitemapLangAction(){
      $sm = $this->_sm;
      $view = new ViewModel();
      $lang = $this->params()->fromRoute('lang');
      $model_languages = new Languages($sm);
      $langs = $model_languages->getAllActive();
      $langs = array_column($langs, 'code');
      if(!in_array($lang, $langs)) return [];
      
      $model_terms = new Terms($sm);
      $terms = $model_terms->getForSitemap();

      header('Content-Type: text/xml');
      return $view->setTerminal(true)->setVariables([
         'terms' => $terms,
         'langs' => $langs,
         'lang' => $lang,
      ]);
   }
   

}

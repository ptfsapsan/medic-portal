<?
namespace Application\Controller;

use Application\Form\Search;
use Application\Model\Languages;
use Application\Model\Terms;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;


class BasicController extends AbstractActionController{

   /**
    * @var ServiceManager
    */
   protected $_sm;
   /**
    * @var FlashMessenger
    */
   protected $_fm;
   protected $_get;
   protected $_post;
   protected $_params;
   protected $_lang;

   public function onDispatch(MvcEvent $e){
      $this->_sm = $e->getApplication()->getServiceManager();
      $post = (array)$this->params()->fromPost();
      $get = (array)$this->params()->fromQuery();
      $this->_get = $get;
      $this->_post = $post;
      $this->_params = array_merge($post, $get);
      $this->_fm = new FlashMessenger();
      $model_languages = new Languages($this->_sm);
      $lang = $model_languages->getCurLang();
      $this->_lang = $lang;
      $session = new Container('basic');
      if(empty($session->search)) $session->search = '';
      if(empty($session->search_term_article))
         $session->search_term_article = 'term';
      if(empty($session->search_title_text))
         $session->search_title_text = 'title';
      $form_search = new Search($this->_sm, $lang, $session);

      $this->layout()->setVariables([
         'langs' => $model_languages->getAllActive(),
         'lang' => $lang,
         'fm' => $this->_fm,
         'form_search' => $form_search,
      ]);

      parent::onDispatch($e);
   }

   protected function error403(){
      $e = $this->getEvent();
      $response = $e->getResponse();
      $response->setStatusCode(403);

      $this->layout()
         ->setTemplate('layout/error')
         ->setVariables([
            'disable_sidebar' => true
         ])
      ;

      $view = new ViewModel();
      $view->setTemplate('error/403');

      return $view;
   }

   protected function error404(){
      $e = $this->getEvent();
      $response = $e->getResponse();
      $response->setStatusCode(404);
      $this->layout()
         ->setTemplate('layout/error')
         ->setVariables([
            'disable_sidebar' => true
         ])
      ;

      $view = new ViewModel();
      $view->setTemplate('error/404');

      return $view;
   }


}

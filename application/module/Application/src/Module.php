<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */



namespace Application;

use Application\Classes\Acl;
use Application\Model\Languages;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;
use Application\View\Helper;

class Module{

   public function onBootstrap(MvcEvent $e){
      $application = $e->getApplication();
      $eventManager = $application->getEventManager();

      $eventManager->attach(MvcEvent::EVENT_ROUTE,
         [$this, 'accessControl'], 1);
      $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR,
         [$this, 'dispatchErrorHandler']);
      $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR,
         [$this, 'renderErrorHandler']);
      $serviceManager = $application->getServiceManager();
      $sessionManager = $serviceManager->get(SessionManager::class);
      $sessionManager->start();
   }

   /**
    * Обработчик для ненайденного пути
    *
    * @param MvcEvent $e
    */
   public function dispatchErrorHandler(MvcEvent $e){
      /** @var ViewModel $layout */
      $layout = $e->getViewModel();
      $layout->setTemplate('layout/error');
   }

   /**
    * Обработчик исключительной ошибки
    *
    * @param MvcEvent $e
    */
   public function renderErrorHandler(MvcEvent $e){
      $layout = $e->getViewModel();
      $layout->setTemplate('layout/error');
   }


   public function getConfig(){
      return include __DIR__ . '/../config/module.config.php';
   }

   private function bootstrapTranslator(ServiceManager $sm, $lang){
      $translator = $sm->get('translator');
      $sm->get('ViewHelperManager')->get('translate')
         ->setTranslator($translator);
      $translator
         ->addTranslationFile("phparray",
            APPLICATION_ROOT.'/module/Application/languages/'.$lang.'.php');

      $translator->setLocale($lang);
   }

   public function accessControl(MvcEvent $e){
      $sm = $e->getApplication()->getServiceManager();
      $route = $e->getRouteMatch();
      $lang = $route->getParam('lang', 'ru');
      $model_languages = new Languages($sm);
      $languages = $model_languages->getAllActive();
      $langs = array_column($languages, 'code');
      if(!in_array($lang, $langs)) $this->redirect($e, 'home');

      if(!$route) $this->redirect($e, 'home');

      $this->bootstrapTranslator($sm, $lang);
      $controller = $route->getParam('controller');

      $auth = new \Zend\Authentication\AuthenticationService();
      $identity = $auth->getIdentity();
      $role = empty($identity) || !in_array($identity['role'], Acl::$roles)?
         Acl::ROLE_GUEST: $identity['role'];
      $acl_model = new Acl();
      $acl = $acl_model->init();

      if(!$acl->isAllowed($role, $controller)) $this->redirect($e, 'home');

      $layout = $e->getViewModel();
      switch($controller){
         case \Application\Controller\IndexController::class:
            $layout->setTemplate('/layout/layout');
            $pages = $sm->get('navigation-main')->getPages();
            foreach($pages as $page) $page->setParams(['lang' => $lang]);

            break;

         case \Application\Controller\AdminController::class:
            $layout->setTemplate('/layout/admin');
            break;
      }
   }

   protected function redirect(MvcEvent $e, $route, $params = []){
      $router = $e->getRouter();
      $url = $router->assemble($params, ['name' => $route]);
      $response = $e->getResponse();
      $response->setStatusCode(302);
      //redirect to login route...
      $response->getHeaders()->addHeaderLine('Location', $url);
      $e->stopPropagation();
   }

   public function getViewHelperConfig(){
      return [
         'invokables' => [
            'paginatorWidget' => Helper\PaginatorWidget::class,
         ]
      ];
   }

}

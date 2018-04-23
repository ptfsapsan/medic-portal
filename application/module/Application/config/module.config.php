<?php
use Application\Classes;
use Application\Controller;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\Db\Adapter\AdapterServiceFactory;
use Zend\I18n\Translator\TranslatorServiceFactory;
use Zend\Log\LoggerAbstractServiceFactory;
use Zend\Session\Storage\SessionArrayStorage;


return [
   'router' => Classes\Router::getRouter(),
   'navigation' => Classes\Navigation::getNavigation(),
   'service_manager' => [
      'factories' => [
         'translator' => TranslatorServiceFactory::class,
         'logger' => LoggerAbstractServiceFactory::class,
         'dbAdapter' => AdapterServiceFactory::class,
         'navigation-main' => DefaultNavigationFactory::class,
         'navigation-admin' => Classes\AdminNavigationFactory::class,
      ],
   ],
   'controllers' => [
      'factories' => [
         Controller\IndexController::class => InvokableFactory::class,
         Controller\SectionController::class => InvokableFactory::class,
         Controller\AdminController::class => InvokableFactory::class,
         Controller\AjaxController::class => InvokableFactory::class,
         Controller\ErrorController::class => InvokableFactory::class,
         Controller\ServiceController::class => InvokableFactory::class,
      ],
   ],
   'session_config' => [
      'cookie_lifetime' => 60*60*24*30,
      'gc_maxlifetime'     => 60*60*24*30*12,
   ],
   'session_manager' => [
      'validators' => [
//         \Zend\Session\Validator\RemoteAddr::class,
//         \Zend\Session\Validator\HttpUserAgent::class,
      ]
   ],
   'session_storage' => [
      'type' => SessionArrayStorage::class
   ],
   'view_manager' => [
      'display_not_found_reason' => true,
      'display_exceptions' => true,
      'doctype' => 'HTML5',
      'not_found_template' => 'error/404',
      'exception_template' => 'error/index',
      'template_map' => Classes\Templates::getTemplateMap(),
      'template_path_stack' => Classes\Templates::getTemplatePathStack(),
   ],
   'mail' => [
      'smtp_options' => [
         'name' => 'localhost.localdomain',
         'host' => 'cpanel4.d.fozzy.com',
         'connection_class' => 'login',
         'connection_config' => [
            'username' => 'transport@medic-portal.com',
            'password' => 'tbDQaqZ,zV}(',
         ],
      ],
      'admin_email' => 'info@mir-talantow.ru',
   ],

   'languages' => [
      'es' => 'Español',
      'en' => 'English',
      'de' => 'Deutsch',
      'ru' => 'Русский',
      'fr' => 'Français',
      'it' => 'Italiano',
   ],
];

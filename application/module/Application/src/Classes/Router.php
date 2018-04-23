<?php

namespace Application\Classes;

use Application\Controller\AdminController;
use Application\Controller\AjaxController;
use Application\Controller\ErrorController;
use Application\Controller\IndexController;
use Application\Controller\SectionController;
use Application\Controller\ServiceController;

class Router{
   public static function getRouter(){
      return [
         'routes' => [
            'home' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/[:lang]',
                  'constraints' => [
                     'lang' => '[a-z]{2}',
                  ],
                  'defaults' => [
                     'controller' => IndexController::class,
                     'action' => 'index',
                     'lang' => 'ru',
                  ],
               ],
            ],
            'section' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/:lang/:section',
                  'constraints' => [
                     'lang' => '[a-z]{2}',
                     'section' => '[a-z-]+',
                  ],
                  'defaults' => [
                     'controller' => SectionController::class,
                     'action' => 'index',
                  ],
               ],
            ],
            'term' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/:lang/:section/term/:title',
                  'constraints' => [
                     'lang' => '[a-z]{2}',
                     'section' => '[a-z-]+',
                     'title' => '[a-z-]+',
                  ],
                  'defaults' => [
                     'controller' => SectionController::class,
                     'action' => 'term',
                  ],
               ],
            ],
            'article' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/:lang/article/:title',
                  'constraints' => [
                     'lang' => '[a-z]{2}',
                     'title' => '[a-z-]+',
                  ],
                  'defaults' => [
                     'controller' => SectionController::class,
                     'action' => 'article',
                  ],
               ],
            ],
            'index' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/:lang/:action',
                  'constraints' => [
                     'action' => '(login|contacts|search)',
                     'lang' => '[a-z]{2}'
                  ],
                  'defaults' => [
                     'controller' => IndexController::class,
                     'action' => 'contacts',
                  ],
               ],
            ],
            'admin' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/admin[/:action[/:id[/:lang]]]',
                  'constraints' => [
                     'action' => '[a-z-]+',
                     'id' => '\d+',
                     'lang' => '[a-z]{2}',
                  ],
                  'defaults' => [
                     'controller' => AdminController::class,
                     'action' => 'languages',
                  ],
               ],
            ],

            'ajax' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/ajax/:action',
                  'constraints' => [
                     'action' => '[a-z-]+',
                  ],
                  'defaults' => [
                     'controller' => AjaxController::class,
                     'action' => 'index',
                  ],
               ],
            ],
            'logout' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/:lang/logout',
                  'constraints' => [
                     'lang' => '[a-z]{2}',
                  ],
                  'defaults' => [
                     'controller' => ServiceController::class,
                     'action' => 'logout',
                     'lang' => 'ru',
                  ],
               ],
            ],
            '404' => [
               'type' => 'Literal',
               'options' => [
                  'route' => '/404',
                  'defaults' => [
                     'controller' => ErrorController::class,
                     'action' => 'no-page',
                  ],
               ],
            ],
            'robots-txt' => [
               'type' => 'Literal',
               'options' => [
                  'route' => '/robots.txt',
                  'defaults' => [
                     'controller' => ServiceController::class,
                     'action' => 'robots',
                  ],
               ],
            ],
            'sitemap-xml' => [
               'type' => 'Literal',
               'options' => [
                  'route' => '/sitemap.xml',
                  'defaults' => [
                     'controller' => ServiceController::class,
                     'action' => 'sitemap',
                  ],
               ],
            ],
            'sitemap-lang' => [
               'type' => 'Segment',
               'options' => [
                  'route' => '/sitemap/:lang',
                  'constraints' => [
                     'lang' => '[a-z]{2}',
                  ],
                  'defaults' => [
                     'controller' => ServiceController::class,
                     'action' => 'sitemap-lang',
                  ],
               ],
            ],

         ],
      ];

   }
}
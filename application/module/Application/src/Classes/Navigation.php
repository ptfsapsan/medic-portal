<?php

namespace Application\Classes;

use Application\Model\Main;

class Navigation{
   public static function getNavigation(){
      $sections = Main::getSections();
      $s = [];
      $s[] = [
         'label' => 'home',
         'route' => 'home',
      ];
      foreach($sections as $title){
         $s[] = [
            'label' => $title,
            'route' => 'section',
         ];
      }

      $s[] = [
         'label' => 'contacts',
         'route' => 'index',
         'action' => 'contacts',
      ];


      return [
         'default' => $s,



         'admin' => [
            [
               'label' => 'Языки',
               'route' => 'admin',
               'action' => 'languages',
            ],
            [
               'label' => 'Разделы',
               'route' => 'admin',
               'action' => 'sections',
            ],
            [
               'label' => 'Статьи',
               'route' => 'admin',
               'action' => 'articles',
            ],
            [
               'label' => 'Термины',
               'route' => 'admin',
               'action' => 'terms',
            ],
            [
               'label' => 'Выход',
               'route' => 'logout',
            ],
         ],

      ];
   }
}
<?php
namespace Application\Classes;

class Templates{

   private static $_email_templates = [
      'from-contacts',
   ];

   public static function getTemplatePathStack(){
      $dir = APPLICATION_ROOT.'/module/Application/view/';
      $res = ['views' => $dir];
      $res['template'] = $dir.'email/layout/template.phtml';

      foreach(self::$_email_templates as $t)
         $res[$t] = $dir.'email/'.$t.'.phtml';

      return $res;
   }
   
   public static function getTemplateMap(){
      $dir = APPLICATION_ROOT.'/module/Application/view/';
      return [
         'layout/layout' => $dir.'layout/layout.phtml',
         'layout/admin' => $dir.'layout/admin.phtml',
         'layout/error' => $dir.'layout/error.phtml',
         'error/404' => $dir.'error/404.phtml',
         'error/403' => $dir.'error/403.phtml',
         'error/index' => $dir.'error/500.phtml',
      ];
   }
   
}
<? namespace Application\Classes;

use Application\Controller\AdminController;
use Application\Controller\AjaxController;
use Application\Controller\ErrorController;
use Application\Controller\IndexController;
use Application\Controller\SectionController;
use Application\Controller\ServiceController;
use Zend\Permissions\Acl\Acl as Permissions;

class Acl extends Permissions{

   const ROLE_GUEST = 'guest';
   const ROLE_ADMIN = 'admin';

   public static $roles = [
      self::ROLE_GUEST,
      self::ROLE_ADMIN,
   ];

   private static $_resources = [
      'index' => IndexController::class,
      'section' => SectionController::class,
      'ajax' => AjaxController::class,
      'admin' => AdminController::class,
      'service' => ServiceController::class,
      'error' => ErrorController::class,
   ];


   public function init(){
      // роли
      $this->addRole(self::ROLE_GUEST);
      $this->addRole(self::ROLE_ADMIN, self::ROLE_GUEST);

      $this
         // ресурсы
         ->addResource(self::$_resources['index'])
         ->addResource(self::$_resources['section'])
         ->addResource(self::$_resources['ajax'])
         ->addResource(self::$_resources['admin'])
         ->addResource(self::$_resources['service'])
         ->addResource(self::$_resources['error'])

         // запрещения
         ->deny()
         // разрешения
         ->allow(self::ROLE_GUEST, [
            self::$_resources['index'],
            self::$_resources['section'],
            self::$_resources['service'],
            self::$_resources['error'],
            self::$_resources['ajax'],
         ])
         ->allow(self::ROLE_ADMIN, [
            self::$_resources['admin'],
         ])

      ;
      return $this;
   }



}

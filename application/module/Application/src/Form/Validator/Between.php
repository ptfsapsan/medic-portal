<?

namespace Application\Form\Validator;


class Between extends \Zend\Validator\Between{

   public function __construct($options){

      $this->messageTemplates = [
         self::NOT_BETWEEN => '',
         self::NOT_BETWEEN_STRICT => '',
      ];
      parent::__construct($options);
   }

}
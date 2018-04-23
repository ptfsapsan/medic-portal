<?php
namespace Application\Form;

use Zend\Form\Form;

class Language extends Form{
   public function __construct(){
      parent::__construct();

      $this
         ->setAttribute('method', 'get')
         ->add([
            'name' => 'act',
            'type' => 'hidden',
            'attributes' => [
               'value' => 'add_language',
            ],
         ])
         ->add([
            'name' => 'code',
            'type' => 'text',
            'options' => [
               'label' => 'Код языка: ',
            ],
            'attributes' => [
               'required' => true,
            ],
         ])
         ->add([
            'name' => 'title',
            'type' => 'text',
            'options' => [
               'label' => 'Название языка: ',
            ],
            'attributes' => [
               'required' => true,
            ],
         ])
         ->add([
            'name' => 'submit',
            'attributes' => [
               'type' => 'submit',
               'value' => 'Добавить',
               'class' => 'button',
            ],
         ])
      ;
   }
}
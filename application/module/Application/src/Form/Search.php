<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\Url;

class Search extends Form{
   public function __construct(ServiceManager $sm, $lang, $session){
      parent::__construct();
      
      $tr = $sm->get('translator');

      $this
         ->setAttributes([
            'method' => 'post',
            'action' => "/$lang/search",
         ])
         ->add([
            'name' => 'act',
            'type' => 'hidden',
            'attributes' => [
               'value' => 'search',
            ],
         ])
         ->add([
            'name' => 'search_term_article',
            'type' => 'radio',
            'options' => [
               'value_options' => [
                  'term' => $tr->translate('поиск в терминах'),
                  'article' => $tr->translate('поиск в статьях'),
               ],
            ],
            'attributes' => [
               'value' => $session->search_term_article,
            ],
         ])
         ->add([
            'name' => 'search',
            'type' => 'text',
            'attributes' => [
               'value' => $session->search,
            ]
         ])
         ->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
               'value' => $tr->translate('Поиск'),
            ],
         ])
         ->add([
            'name' => 'search_title_text',
            'type' => 'radio',
            'options' => [
               'value_options' => [
                  'title' => $tr->translate('поиск в заголовках'),
                  'text' => $tr->translate('поиск в текстах'),
               ],
            ],
            'attributes' => [
                'value' => $session->search_title_text,
            ],
         ])
      ;
   }
}
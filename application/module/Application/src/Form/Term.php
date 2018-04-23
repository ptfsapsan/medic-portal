<?php
namespace Application\Form;

use Application\Model\Languages;
use Application\Model\Terms;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

class Term extends Form{
   public function __construct(ServiceManager $sm, $term_id, $lang){
      parent::__construct();

      $model_terms = new Terms($sm);
      $term = $model_terms->getByIdAndLang($term_id, $lang);
      $term['term_id'] = $term_id;
      $term['lang'] = $lang;
      $model_languages = new Languages($sm);
      $languages = $model_languages->getAll();
      $this
         ->add([
            'name' => 'act',
            'type' => 'hidden',
            'attributes' => [
               'value' => 'edit_term_texts',
            ],
         ])
         ->add([
            'name' => 'term_id',
            'type' => 'hidden',
         ])
         ->add([
            'name' => 'lang',
            'type' => 'select',
            'options' => [
               'value_options' => array_column($languages, 'title', 'code'),
            ],
         ])
         ->add([
            'name' => 'title',
            'type' => 'text',
            'options' => [
               'label' => 'Название',
            ],
         ])
         ->add([
            'name' => 'short_text',
            'type' => 'textarea',
            'options' => [
               'label' => 'Краткий текст',
            ],
         ])
         ->add([
            'name' => 'text',
            'type' => 'textarea',
            'options' => [
               'label' => 'Текст',
            ],
         ])
         ->add([
            'name' => 'meta_title',
            'type' => 'text',
            'options' => [
               'label' => 'Title',
            ],
         ])
         ->add([
            'name' => 'meta_keywords',
            'type' => 'text',
            'options' => [
               'label' => 'Keywords',
            ],
         ])
         ->add([
            'name' => 'meta_description',
            'type' => 'text',
            'options' => [
               'label' => 'Description',
            ],
         ])
         ->add([
            'name' => 'submit',
            'attributes' => [
               'type' => 'submit',
               'value' => 'Сохранить',
            ],
         ])
      ;
      $this->populateValues($term);
   }
}
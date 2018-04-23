<?php
namespace Application\Form;

use Application\Model\Languages;
use Application\Model\Main;
use Application\Model\SectionTexts;
use Application\Model\Terms;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

class Section extends Form{
   public function __construct(ServiceManager $sm, $section_id, $lang){
      parent::__construct();

      $model_section_texts = new SectionTexts($sm);
      $text = $model_section_texts->getBySectionIdAndLang($section_id, $lang);
      $text['section_id'] = $section_id;
      $text['lang'] = $lang;

      $model_languages = new Languages($sm);
      $languages = $model_languages->getAll();

      $model_main = new Main($sm);

      $sections = [
         10 => 'Главная страница',
         11 => 'Контакты',
      ] + $model_main->getTrSections();

      $this
         ->add([
            'name' => 'act',
            'type' => 'hidden',
            'attributes' => [
               'value' => 'edit_section',
            ],
         ])
         ->add([
            'name' => 'section_id',
            'type' => 'select',
            'options' => [
               'value_options' => $sections,
            ],
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
      $this->populateValues($text);
   }
}
<?php
namespace Application\Form;

use Application\Model\Articles;
use Application\Model\Languages;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManager;

class Article extends Form{

   /**
    * @var ServiceManager
    */
   private $sm;
   
   public function __construct(ServiceManager $sm){
      parent::__construct();
      
      $this->sm = $sm;
      $model_languages = new Languages($sm);
      $languages = $model_languages->getAll();
      $this
         ->add([
            'name' => 'act',
            'type' => 'hidden',
            'attributes' => [
               'value' => 'add_article',
            ],
         ])
         ->add([
            'name' => 'article_id',
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
   }
   
   public function prepareForEdit($article_id, $lang){
      $model_articles = new Articles($this->sm);
      $article = $model_articles->getByIdAndLang($article_id, $lang);
      $this->populateValues($article);
      $this->get('lang')->setValue($lang)
         ->setAttribute('onchange', 'location.href="/admin/article/'.
            $article_id.'/" + this.value');
      $this->get('act')->setValue('edit_article');
   }
}
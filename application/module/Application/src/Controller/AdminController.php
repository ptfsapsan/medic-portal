<?php

namespace Application\Controller;

use Application\Form\Article;
use Application\Form\Language;
use Application\Form\Section;
use Application\Form\Term;
use Application\Model\Articles;
use Application\Model\Languages;
use Application\Model\Main;
use Application\Model\SectionTexts;
use Application\Model\Terms;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;

class AdminController extends AbstractActionController{

   /**
    * @var ServiceManager
    */
   protected $_sm;
   /**
    * @var FlashMessenger
    */
   protected $_fm;
   protected $_get;
   protected $_post;

   public function onDispatch(MvcEvent $e){
      $this->_sm = $e->getApplication()->getServiceManager();
      $this->_post = (array)$this->params()->fromPost();
      $this->_get = (array)$this->params()->fromQuery();
      $this->_fm = $this->flashMessenger();

      $this->layout()->setVariables([
         'fm' => $this->_fm,
      ]);

      parent::onDispatch($e);
   }

   public function articlesAction(){
      $sm = $this->_sm;
      $get = $this->_get;
      $post = $this->_post;
      $session = new Container('adminArticles');
      $model_articles = new Articles($sm);
      $fm = $this->_fm;

      if(!empty($get['act'])){
         switch($get['act']){
            case 'ch_page': $session->page = $get['page'];
               break;
            case 'delete': $model_articles->delArticle($get['id']);
               break;
            case 'ch_active': $model_articles->changeActive($get['id']);
               break;
         }
         $this->redirect()->refresh();
      }
      
      if(!empty($post['act'])){
         switch($post['act']){
            case 'add_article':
               try{
                  $model_articles->addArticle($post['title_ru']);
               }
               catch(\Exception $e){
                  $fm->addErrorMessage($e->getMessage());
               }
               break;
         }
         $this->redirect()->refresh();
      }

      if(empty($session->page)) $session->page = 1;
      if(empty($session->on_page)) $session->on_page = 20;

      return [
         'articles' => $model_articles->getAll($session),
      ];
   }

   public function articleAction(){
      $sm = $this->_sm;
      $post = $this->_post;
      $fm = $this->_fm;
      $id = $this->params()->fromRoute('id');
      $lang = $this->params()->fromRoute('lang', 'ru');
      $model_languages = new Languages($sm);
      if(empty($id) || !$model_languages->isCode($lang))
         $this->redirect()->toRoute('admin', ['action' => 'articles']);
      $model_articles = new Articles($sm);
      $form_article = new Article($sm);
      $form_article->prepareForEdit($id, $lang);
      
      $model_terms = new Terms($sm);

      if(!empty($post['act'])){
         switch($post['act']){
            case 'edit_article':
               try{
                  $model_articles->editArticleTexts($post, $id, $lang);
               }
               catch(\Exception $e){
                  $fm->addErrorMessage($e->getMessage());
               }
               break;
         }
         $this->redirect()->refresh();
      }

      return [
         'form_article' => $form_article,
         'terms' => $model_terms->getAllForAdmin(),
         'article_id' => $id,
      ];
   }

   public function languagesAction(){
      $sm = $this->_sm;
      $get = $this->_get;
      $fm = $this->_fm;
      $model_languages = new Languages($sm);
      $form_language = new Language();

      if(!empty($get['act'])){
         switch($get['act']){
            case 'delete': $model_languages->deleteLanguage($get['id']);
               break;
            case 'add_language': $model_languages->addLanguage($get);
               break;
            case 'ch_active': $model_languages->changeActive($get['id']);
               break;
         }
         $this->redirect()->refresh();
      }
      
      return [
         'form_language' => $form_language,
         'languages' => $model_languages->getAll(),
      ];
   }
   
   public function termsAction(){
      $sm = $this->_sm;
      $get = $this->_get;
      $session = new Container('adminTerms');

      if(empty($session->page)) $session->page = 1;
      $sections = Main::getSections();
      if(empty($session->section_id)) $session->section_id = key($sections);
      $session->on_page = 20;
      $model_terms = new Terms($sm);

      if(!empty($get['act'])){
         switch($get['act']){
            case 'ch_section_id': $session->section_id = $get['id'];
                                  $session->page = 1;
               break;
            case 'ch_page': $session->page = $get['page'];
               break;
            case 'add_term': $model_terms->addTerm($get);
               break;
            case 'delete': $model_terms->delTerm($get['id']);
               break;
            case 'ch_active': $model_terms->changeActive($get['id']);
               break;
         }
         $this->redirect()->refresh();
      }
      
      return [
         'sections' => $sections,
         'section_id' => $session->section_id,
         'terms' => $model_terms->getAll($session),
      ];
   }
   
   public function termAction(){
      $sm = $this->_sm;
      $post = $this->_post;
      $id = $this->params()->fromRoute('id');
      $lang = $this->params()->fromRoute('lang', 'ru');
      if(empty($id)) $this->redirect()->toRoute('admin', ['action' => 'terms']);
      $form_term = new Term($sm, $id, $lang);
      $model_terms = new Terms($sm);

      if(!empty($post['act'])){
         switch($post['act']){
            case 'edit_term_texts': $model_terms->editTermTexts($post);
               break;
         }
         $this->redirect()->refresh();
      }

      return [
         'form_term' => $form_term,
      ];
   }


   public function sectionsAction(){
      $sm = $this->_sm;
      $post = $this->_post;
      $section_id = $this->params()->fromRoute('id', 1);
      $lang = $this->params()->fromRoute('lang', 'ru');
      $model_section_texts = new SectionTexts($sm);
      $form_section = new Section($sm, $section_id, $lang);

      if(!empty($post['act'])){
         switch($post['act']){
            case 'edit_section': $model_section_texts->editSection($post);
               break;
         }
         $this->redirect()->refresh();
      }

      return [
         'form_section' => $form_section,
      ];
   }
   
}

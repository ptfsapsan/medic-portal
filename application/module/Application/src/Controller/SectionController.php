<?php
namespace Application\Controller;


use Application\Model\Articles;
use Application\Model\Main;
use Application\Model\SectionTexts;
use Application\Model\Terms;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class SectionController extends BasicController{

   public function indexAction(){
      $sm = $this->_sm;
      $lang = $this->_lang;
      $section = $this->params()->fromRoute('section');
      $section_id = Main::getSectionNumber($section);
      $model_section_texts = new SectionTexts($sm);
      $section_texts = 
         $model_section_texts->getBySectionIdAndLang($section_id, $lang);

      $view_model = new ViewModel();
      $glossary_view = $this->glossaryView($section_id, $section);
      return $view_model
         ->addChild($glossary_view, 'glossary')
         ->setVariables([
            'section_texts' => $section_texts,
            'section_id' => $section_id,
         ])
      ;
   }


   public function termAction(){
      $sm = $this->_sm;
      $lang = $this->_lang;
      $section = $this->params()->fromRoute('section');
      $this->layout()->section = $section;
      $term_title = $this->params()->fromRoute('title');
      $model_terms = new Terms($sm);
      $model_articles = new Articles($sm);
      $term = $model_terms->getByTitleAndLang($term_title, $lang);
      if(empty($term)) $this->error404();

      $glossary_view = $this->glossaryView($term['section_id'], $section);
      $glossary_view->setVariable('term',
         $model_terms->getById($term['term_id']));

      $view_model = new ViewModel();
      return $view_model
         ->addChild($glossary_view, 'glossary')
         ->setVariables([
            'term' => $term,
            'section' => $section,
            'articles' =>
               $model_articles->getArticlesByTerms($term['id'], $lang),
         ])
      ;
   }

   public function articleAction(){

   }








   private function glossaryView($section_id, $section){
      $sm = $this->_sm;
      $lang = $this->_lang;
      $model_terms = new Terms($sm);
      $term_letters = $model_terms->getLetters($lang, $section_id);
      $letters = array_column($term_letters, 'letter');
      $term_title = $this->params()->fromRoute('title');

      // выбираем другую букву
      $letter = $this->params()->fromQuery('letter');
      if(!empty($letter)){
         $term = $model_terms->getByTitleAndLang($term_title, $lang);
         if($letter != $term['letter']){
            $first_term = $model_terms->getFirstTerm($lang, $letter);
            $this->redirect()->toRoute('term', [
               'section' => $section,
               'title' => $first_term['title'],
               'lang' => $lang,
            ]);
         }
      }
      $term = $model_terms->getByTitleAndLang($term_title, $lang);
      $letter = empty($term) || !in_array($term['letter'], $letters)?
         current($letters): $term['letter'];

      $view_model = new ViewModel();
      return $view_model
         ->setTemplate('application/section/glossary')
         ->setVariables([
            'term_letters' => $letters,
            'section_id' => $section_id,
            'term_letter' => $letter,
            'terms' => $model_terms->getByLangAndLetter($section_id, $lang,
               $letter),
            'section' => $section,
         ])
      ;
   }

}

<?php
namespace Application\Controller;


use Application\Form\Contacts;
use Application\Form\Filter\LoginFilter;
use Application\Form\Login;
use Application\Model\Articles;
use Application\Model\Auth;
use Application\Model\Mail;
use Application\Model\Main;
use Application\Model\SectionTexts;
use Application\Model\Terms;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends BasicController{

   public function indexAction(){
      $sm = $this->_sm;
      $get = $this->_get;
      $model_section_texts = new SectionTexts($sm);
      $section_id = 10;
      $lang = $this->_lang;

      return [
         'lang' => $lang,
         'item' =>
            $model_section_texts->getBySectionIdAndLang($section_id, $lang),
      ];
   }

   public function contactsAction(){
      $sm = $this->_sm;
      $post = $this->_post;
      $form_contacts = new Contacts();
      $model_section_texts = new SectionTexts($sm);
      $section_id = 11;
      $lang = $this->_lang;


      if(!empty($post['act'])){
         switch($post['act']){
            case 'add_message': $model_mail = new Mail($sm);
                 $subject = 'Письмо с medic-portal';
                 $model_mail->sendView(null, $subject, 'from-contacts', $post);
               break;
         }
         $this->redirect()->refresh();
      }


      return [
         'form_contacts' => $form_contacts,
         'item' => $model_section_texts->getBySectionIdAndLang($section_id, $lang),

      ];
   }

   /**
    * @return array|\Zend\Http\Response
    */
   public function loginAction(){
      $sm = $this->_sm;
      $fm = $this->_fm;
      $post = $this->_post;
      $form_login = new Login();
      $model_auth = new Auth($sm);

      if(!empty($post['act'])){
         switch($post['act']){
            case 'login':
               $form_login->setData($post);
               $form_login->setInputFilter(new LoginFilter($sm));
               if($form_login->isValid()){
                  try{
                     $model_auth->login($form_login->getData());
                     return $this->redirect()->toRoute('admin');
                  }
                  catch(\Exception $e){
                     $fm->addErrorMessage($e->getMessage());
                  }
               }
               else{
                  $fm->addErrorMessage($form_login->getMessages());
               }
               break;
         }
         $this->redirect()->refresh();
      }

      return [
         'form_login' => $form_login,
      ];
   }

   public function searchAction(){
      $sm = $this->_sm;
      $fm = $this->_fm;
      $get = $this->_get;
      $post = $this->_post;
      $session = new Container('basic');
      $lang = $this->layout()->lang;

      if(!empty($post['act'])){
         switch($post['act']){
            case 'search': $session->search = $post['search'];
               $session->search_term_article = $post['search_term_article'];
               $session->search_title_text = $post['search_title_text'];
               $session->page = 1;
               break;
         }
         $this->redirect()->refresh();
      }

      if(!empty($get['act'])){
         switch($get['act']){
            case 'ch_page': $session->page = $get['page'];
               break;
         }
         $this->redirect()->refresh();
      }

      $model = $session->search_term_article == 'term'?
         new Terms($sm): new Articles($sm);
      $session->on_page = 10;

      return [
         'items' => $model->getForSearch($session, $lang),
      ];
   }
}

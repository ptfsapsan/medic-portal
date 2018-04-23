<?

namespace Application\Model;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Renderer\PhpRenderer;
use Zend\Validator\EmailAddress;
use Zend\View\Model\ViewModel;
use Zend\View\Resolver;


/**
 * Class Mail
 *
 * @package Application\Model
 */
class Mail{
   /**
    * @var
    */
   private $_sm;

   /**
    * @var
    */
   private $_config;

   /**
    * Mail constructor.
    *
    * @param $sm
    */
   public function __construct(ServiceManager $sm){
      $this->_sm = $sm;
      $this->_config = $sm->get('config');
   }

   /**
    * отправка письма из шаблона
    *
    * @param $to
    * @param $subject
    * @param $template_name
    * @param $params
    *
    * @throws \Exception
    */
   public function sendView($to, $subject, $template_name, $params){
      $config = $this->_config;
      $to = $this->verTo($to);
      $body = self::getBodyFromTemplate($template_name, $params);

      // Собственно сообщение
      $message = new Message();
      $message
         ->setEncoding("UTF-8")
         ->addFrom($config['mail']['admin_email'], "medic-portal.com")
         ->addTo($to)
         ->setBody($body)
         ->setSubject($subject);

      // Отправка
      $options = $config['mail']['smtp_options'];
      $smtpOptions = new SmtpOptions($options);

      $transport = new Smtp($smtpOptions);

      try{
         $transport->send($message);
      }
      catch(\Exception $e){
//         throw new \Exception($e->getMessage());
         throw new \Exception(
            'Сообщение не отправлено. Попробуйте еще раз '.
            'через некоторое время.'
         );
      }
   }


   /**
    * @param $email
    *
    * @return mixed
    * @throws \Exception
    */
   private function verTo($email){
      if(empty($email)){
         $config = $this->_config;
         $email = $config['mail']['admin_email'];
      }

      $v = new EmailAddress();
      if($v->isValid($email)){
         return $email;
      }
      throw new \Exception('Неверный адрес отправки письма');
   }

   /**
    * @param $template_name
    * @param $params
    *
    * @return MimeMessage
    * @throws \Exception
    */
   private function getBodyFromTemplate($template_name, $params){
      $config = $this->_config;
      $params['admin_email'] = $config['mail']['admin_email'];
      $view = new ViewModel(['data' => $params]);

      // Подготовка к рендерингу шаблонов письма
      $renderer = new PhpRenderer();
      $resolver = new Resolver\AggregateResolver();
      $renderer->setResolver($resolver);

      $templateMapResolver = new Resolver\TemplateMapResolver(
         $config['view_manager']['template_path_stack']
      );
      $templatePathStack = new Resolver\TemplatePathStack();
      $resolver->attach($templateMapResolver)
         ->attach($templatePathStack);

      // вариант письма html
      $view->setTemplate($template_name);
      $htmlContent = $renderer->render($view);
      $viewHtml = new ViewModel([
         'content' => $htmlContent,
      ]);
      $viewHtml->setTemplate('template');
      $htmlContent = $renderer->render($viewHtml);
      $messageHtmlContent = new MimePart($htmlContent);
      $messageHtmlContent->type = Mime::TYPE_HTML;
      $messageHtmlContent->setCharset('UTF-8');

      $messageBody = new MimeMessage();
      $messageBody->setParts([$messageHtmlContent]);

      return $messageBody;
   }

   


}
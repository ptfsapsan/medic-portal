<?php
namespace Application\Model;

class Main extends Base{

   private static $sections = [
      //1 => 'health',
      2 => 'diseases',
      //3 => 'medications',
      //4 => 'dietology',
     //5 => 'ethnoscience',
      //6 => 'anatomy',
      //7 => 'news'
   ];
   
   public static function getSections(){
      return self::$sections;
   }
   
   public function getTrSections(){
      $res = [];
      $tr = $this->getTranslator();
      foreach(self::$sections as $key => $section){
         $res[$key] = $tr->translate('menu_'.$section);
      }
      return $res;
   }
   
   public static function getSectionNumber($title){
      return array_search($title, self::$sections);
   }

   public function changeLang($lang){
      $translator = $this->getTranslator();
      $this->_sm->get('ViewHelperManager')->get('translate')
         ->setTranslator($translator);
      $translator
         ->addTranslationFile("phparray", __DIR__.'/../languages/'.$lang.'.php');
      $translator->setLocale($lang);
   }

   // транслитерация
   public static function translit($str){
      $trans = [
         "а" => "a",
         "б" => "b",
         "в" => "v",
         "г" => "g",
         "д" => "d",
         "е" => "e",
         "ё" => "yo",
         "ж" => "zh",
         "з" => "z",
         "и" => "i",
         "й" => "j",
         "к" => "k",
         "л" => "l",
         "м" => "m",
         "н" => "n",
         "о" => "o",
         "п" => "p",
         "р" => "r",
         "с" => "s",
         "т" => "t",
         "у" => "u",
         "ф" => "f",
         "х" => "h",
         "ц" => "c",
         "ч" => "ch",
         "ш" => "sh",
         "щ" => "shch",
         "ы" => "y",
         "э" => "e",
         "ю" => "u",
         "я" => "ya",
         "А" => "A",
         "Б" => "B",
         "В" => "V",
         "Г" => "G",
         "Д" => "D",
         "Е" => "E",
         "Ё" => "Yo",
         "Ж" => "Zh",
         "З" => "Z",
         "И" => "I",
         "Й" => "J",
         "К" => "K",
         "Л" => "L",
         "М" => "M",
         "Н" => "N",
         "О" => "O",
         "П" => "P",
         "Р" => "R",
         "С" => "S",
         "Т" => "T",
         "У" => "U",
         "Ф" => "F",
         "Х" => "H",
         "Ц" => "C",
         "Ч" => "Ch",
         "Ш" => "Sh",
         "Щ" => "Shch",
         "Ы" => "y",
         "Э" => "E",
         "Ю" => "U",
         "Я" => "Ya",
         "ь" => "",
         "Ь" => "",
         "ъ" => "",
         "Ъ" => "",
         " " => "-",
         "," => "-",
         "." => "-",
         "'" => "`",
         '"' => "`"
      ];

      return strtolower(strtr(stripcslashes($str), $trans));
   }

   public static function encrypt($value){
      $key = 'asdwefrge32fs23g3423dvsd546f';
      $sValue = serialize($value);

      return base64_encode(
         mcrypt_encrypt(
            MCRYPT_RIJNDAEL_256, md5($key), $sValue, MCRYPT_MODE_CBC, md5(md5($key))
         )
      );
   }

   public static function decrypt($sValue){
      $key = 'asdwefrge32fs23g3423dvsd546f';
      $sValue = str_replace(' ', '+', $sValue);
      try{
         return unserialize(
            rtrim(
               mcrypt_decrypt(
                  MCRYPT_RIJNDAEL_256, md5($key), base64_decode($sValue),
                  MCRYPT_MODE_CBC, md5(md5($key))
               ), "\0"
            )
         );
      } catch(\Exception $e){
         return null;
      }
   }

   public static function generateCode($nLength = 7){
      $randStr = '';
      $feed = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      for($i = 0; $i < $nLength; $i++){
         $randStr .= substr($feed, rand(0, strlen($feed) - 1), 1);
      }

      return $randStr;
   }

   public static function generateDigitCode($nLength = 6){
      $randStr = '';
      $feed = "123456789";
      for($i = 0; $i < $nLength; $i++){
         $randStr .= substr($feed, rand(0, strlen($feed) - 1), 1);
      }

      return $randStr;
   }

}
<?php
namespace Application\Model;

use Zend\Dom\Document;
use Zend\Dom\Document\Query;

class Parser extends Base{

   private static $yandex_api_key = 'trnsl.1.1.20170404T094436Z.ba3aa32efbdcfd6e.59e7db49783a240ae04fef98539585f0d027edc2';

   public function zdorovieinfo(){
      $domain = 'http://www.zdorovieinfo.ru/bolezni/?&p=';
      $t = $this->fetchAll("SELECT * FROM term_texts WHERE lang = 'ru'");
      $terms = array_column($t, 'title');

      $n = 51;
      while(true){
         $dom = new Document(file_get_contents($domain.$n));
         $results = Query::execute('.center .box .ismed_list a',
            $dom, 'TYPE_CSS');
         foreach($results as $result){
            $title = trim(strip_tags($result->nodeValue));
            if(in_array($title, $terms)) continue;

            $this->insert('terms', [
               'title' => Main::translit($title),
               'section_id' => 2,
            ]);
            $id = $this->lastInsertId();
            $dom_2 = new Document(file_get_contents($result->getAttribute('href')));
            $nn = 0;
            $text = $desc = '';
            while(true){
               $r = Query::execute('#spoilerDiv'.$nn.' div', $dom_2, 'TYPE_CSS');
               if($r->count() == 0) break;

               $t = trim($r->current()->nodeValue);
               if($nn == 0) $desc = mb_substr($t, 0, 1000);
               $text .= '<p>'.$t.'</p>';
               $nn++;
            }

            $this->insert('term_texts', [
               'term_id' => $id,
               'lang' => 'ru',
               'title' => $title,
               'short_text' => $desc,
               'text' => $text,
               'meta_title' => $title,
               'meta_keywords' => $title,
               'meta_description' => $title,
            ]);
         }
         $n++;


         if($n >= 51) break;
      }




      
   }

   public function translateTerms(){
      $terms = $this->fetchAll("SELECT * FROM term_texts WHERE lang = 'ru'");
      $model_langs = new Languages($this->_sm);
      $langs = $model_langs->getAll();
      $langs = array_column($langs, 'code');
      foreach($terms as $term){
         $term_id = $term['term_id'];
         foreach($langs as $lang){
            if($lang == 'ru') continue;
            $t = $this->fetchRow("SELECT * FROM term_texts
               WHERE term_id = ? AND lang = ?", [$term_id, $lang]);
            if(!empty($t['text'])) continue;

            $title = self::curl($lang, $term['title']);

            //var_dump($title);die;
            if(empty($title)) continue;

            $short_text = self::curl($lang, $term['short_text']);
            if(empty($short_text)) continue;

            $text = self::curl($lang, $term['text'], 'html');
            if(empty($text)) continue;

            $d = [
               'letter' => strtoupper(mb_substr($title, 0, 1)),
               'title' => $title,
               'short_text' => $short_text,
               'text' => $text,
               'meta_title' => $title,
               'meta_keywords' => $title,
               'meta_description' => $title,
            ];

            if(!empty($t))
               $this->update('term_texts', $d, "id = {$t['id']}");
            else{
               $d['term_id'] = $term_id;
               $d['lang'] = $lang;
               $this->insert('term_texts', $d);
            }
         }
      }


   }
   
   private static function  curl($lang, $text, $format = 'text'){
      $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
      $params = [
         'key' => self::$yandex_api_key,
         'text' => $text,
         'lang' => 'ru-'.$lang,
         'format' => $format == 'html'? 'html': 'text',
      ];
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $res = curl_exec($ch);
      curl_close($ch);

      if(empty($res)) return false;
      $result = json_decode($res, true);
      if(empty($result['code'])) return false;
      if($result['code'] != 200) die($result['message']);

      return urldecode(current($result['text']));
   }
}
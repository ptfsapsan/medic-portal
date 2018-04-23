<?php
namespace Application\Model;

/**
 * Class Terms
 *
 * @package Application\Model
 */
class Terms extends Base{

   /**
    * @var string
    */
   private $table = 'terms';
   /**
    * @var string
    */
   private $table_texts = 'term_texts';
   /**
    * @var string
    */
   private $table_2article = 'article2term';

   /** добавляем термин
    *
    * @param array $params
    */
   public function addTerm(array $params){
      $this->insert($this->table, [
         'section_id' => $params['section_id'],
         'title' => $params['title'],
      ]);
   }

   /** получаем все термины на русском для админки
    *
    * @return array
    */
   public function getAllForAdmin(){
      return $this->fetchAll("SELECT id, title ru_term_title 
         FROM $this->table_texts WHERE lang = 'ru'");
   }

   /** получаем термины статьи
    *
    * @param int $article_id
    *
    * @return array
    */
   public function getTermsByArticleId($article_id){
      return $this->fetchAll("SELECT ta.id,
         (SELECT title FROM $this->table_texts
            WHERE term_id = ta.term_id AND lang = 'ru') ru_term_title
         FROM $this->table_2article ta WHERE ta.article_id = ?", $article_id);
   }

   /** добавляем термин статье
    *
    * @param int $article_id
    * @param int $term_id
    */
   public function addTermToArticle($article_id, $term_id){
      $a2t = $this->fetchRow("SELECT * FROM $this->table_2article
         WHERE article_id = ? AND term_id = ?", [$article_id, $term_id]);
      if(!empty($a2t)) return;
      $this->insert($this->table_2article, [
         'article_id' => $article_id,
         'term_id' => $term_id,
      ]);
   }

   /** удаляем термин у статьи
    *
    * @param int $a2t_id
    */
   public function delTermFromArticle($a2t_id){
      $this->delete($this->table_2article, "id = $a2t_id");
   }

   /** получаем все термины
    *
    * @param object $session
    *
    * @return array
    */
   public function getAll($session){
      $section_id = (int)$session->section_id;
      $page = (int)$session->page;
      $on_page = (int)$session->on_page;
      $where = "WHERE section_id = $section_id";
      $res = $this->forPagingData2($page, $on_page, $this->table, $where);
      if(empty($res['count'])) return $res;

      $res['data'] = $this->fetchAll("SELECT * FROM $this->table $where
         ORDER BY title LIMIT ?, ?",
         [($page - 1) * $on_page, $on_page]);
      return $res;
   }

   /** удаляем термин
    *
    * @param int $id
    */
   public function delTerm($id){
      $id = (int)$id;
      $this->delete($this->table, "id = $id");
      $this->delete($this->table_texts, "term_id = $id");
   }

   /** получаем термин по id
    *
    * @param int $id
    *
    * @return array|bool
    */
   public function getById($id){
      $id = (int)$id;
      return $this->fetchRow("SELECT * FROM $this->table WHERE id = ?", $id);
   }

   /**
    * @param int $id
    * @param string $lang
    *
    * @return array|bool
    */
   public function getByIdAndLang($id, $lang){
      $id = (int)$id;
      return $this->fetchRow("SELECT * FROM $this->table t
         LEFT JOIN $this->table_texts tt ON tt.term_id = t.id AND tt.lang = ?
         WHERE t.id = ?", [$lang, $id]);
   }

   /**
    * @param int $id
    */
   public function changeActive($id){
      $term = $this->getById($id);
      if(empty($term)) return;

      $this->update($this->table, [
         'active' => $term['active'] == 'yes'? 'no': 'yes',
      ], "id = $id");
   }

   /**
    * @param array $params
    */
   public function editTermTexts(array $params){
      $term_id = (int) $params['term_id'];
      $lang = $params['lang'];
      unset($params['act']);
      unset($params['submit']);
      $params['letter'] = mb_substr($params['title'], 0, 1);
      $texts = $this->fetchRow("SELECT * FROM $this->table_texts
         WHERE term_id = ? AND lang = ?", [$term_id, $lang]);
      if(empty($texts))
         $this->insert($this->table_texts, $params);
      else $this->update($this->table_texts, $params, "id = {$texts['id']}");
   }

   /**
    * @param string $lang
    * @param int $section_id
    *
    * @return array
    */
   public function getLetters($lang, $section_id){
      return $this->fetchAll("SELECT DISTINCT(letter) letter
         FROM $this->table_texts WHERE lang = ? AND 
         (SELECT active FROM $this->table WHERE id = $this->table_texts.term_id
            AND section_id = ? AND active = 'yes') = 'yes' ORDER BY letter",
         [$lang, $section_id]);
   }

   /**
    * @param int $section_id
    * @param string $lang
    * @param string $letter
    *
    * @return array
    */
   public function getByLangAndLetter($section_id, $lang, $letter){
      $letter = mb_substr(trim($letter), 0, 1);
      $res = $this->fetchAll("SELECT t.*, tt.title term_title, tt.lang
         FROM $this->table_texts tt
         LEFT JOIN $this->table t ON t.id = tt.term_id
         WHERE tt.lang = ? AND tt.letter = ? AND t.section_id = ?
         ORDER BY tt.title",
         [$lang, $letter, $section_id]);
      return $res;
   }

   /**
    * @param string $title
    * @param string $lang
    *
    * @return array|bool
    */
   public function getByTitleAndLang($title, $lang){
      return $this->fetchRow("SELECT * FROM $this->table t
         LEFT JOIN $this->table_texts tt ON tt.term_id = t.id AND tt.lang = ?
         WHERE t.title = ?", [$lang, $title]);
   }

   /**
    * @return array
    */
   public function getForSitemap(){
      return $this->fetchAll("SELECT t.title link, t.section_id 
         FROM $this->table_texts tt
         LEFT JOIN $this->table t ON t.id = tt.term_id
         WHERE t.active = 'yes'");
   }

   /**
    * @param object $session
    * @param string $lang
    *
    * @return array
    */
   public function getForSearch($session, $lang){
      $where = empty($session->search)? "WHERE 0":
         "WHERE tt.lang = '$lang'
          AND MATCH(tt.{$session->search_title_text}) AGAINST(".
            $this->quoteValue($session->search).")";

      $count = $this->fetchOne("SELECT COUNT(*) FROM $this->table_texts tt
         $where");
      $page = (int)$session->page;
      $on_page = (int)$session->on_page;
      $res = $this->forPagingData($session, $count);
      $res['type'] = 'term';
      if(empty($count)) return $res;

      $res['data'] = $this->fetchAll("SELECT tt.*, t.title link, t.section_id
         FROM $this->table_texts tt
         LEFT JOIN $this->table t ON t.id = tt.term_id $where
         LIMIT ?, ?", [($page - 1) * $on_page, $on_page]);
      return $res;
   }

   /**
    * @param string $lang
    * @param string $letter
    *
    * @return array|bool
    */
   public function getFirstTerm($lang, $letter){
      return $this->fetchRow("SELECT t.* FROM $this->table t
         LEFT JOIN $this->table_texts tt ON tt.term_id = t.id AND tt.lang = ?
         WHERE tt.letter = ? ORDER BY tt.letter LIMIT 1",
         [$lang, $letter]);
   }


   /**
    *
    */
   public function setA(){
      $terms = $this->fetchAll("SELECT tt.*, t.title link, t.section_id
       FROM $this->table_texts tt
       LEFT JOIN $this->table t ON t.id = tt.term_id");

      foreach($terms as $term2){
         $t = $term2['title'];
         $tt = strtolower($term2['title']);
         $section = Main::getSections()[$term2['section_id']];
         $lang = $term2['lang'];
         $a = '<a href="/'.$lang.'/'.$section.'/term/'.$term2['link'].'">';
         foreach($terms as $term){
            if($lang != $term['lang']) continue;
            $text = $term['text'];
            $source = [" $t ", " $t,", " $t.", " $tt ", " $tt,", " $tt."];
            $dist = [
               ' '.$a.$t.'</a> ',
               ' '.$a.$t.'</a>,',
               ' '.$a.$t.'</a>.',
               ' '.$a.$tt.'</a> ',
               ' '.$a.$tt.'</a>,',
               ' '.$a.$tt.'</a>.',
            ];
            $res = str_replace($source, $dist, $text);
            if($res != $text){
               $this->update($this->table_texts, ['text' => $res],
                  "id = {$term['id']}");
            }
         }
      }
   }

   /**
    *
    */
   public function setA2(){
      $list = $this->fetchAll("SELECT * FROM $this->table_texts");
      foreach($list as $i){
         $source = ['<p>', '<P>', '</p>', '</ p>', '</P>', '</ P>'];
         $dist = ['<div>', '<div>', '</div>', '</div>', '</div>', '</div>'];
         $text = str_replace($source, $dist, $i['text']);
         //var_dump($text);die;
         if($text != $i['text'])
         $this->update($this->table_texts, ['text' => $text],
            "id = {$i['id']}");
      }


   }

}
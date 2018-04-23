<?php
namespace Application\Model;

/**
 * Class Articles
 *
 * @package Application\Model
 */
/**
 * Class Articles
 *
 * @package Application\Model
 */
class Articles extends Base{

   /**
    * @var string
    */
   private $table = 'articles';
   /**
    * @var string
    */
   private $table_texts = 'article_texts';

   /**
    * @param $title_ru
    *
    * @throws \Exception
    */
   public function addArticle($title_ru){
      if(empty($title_ru)) throw new \Exception('empty_title');
      $translit = Main::translit($title_ru);
      $n = 0;
      while(true){
         $f = $this->fetchRow("SELECT * FROM $this->table WHERE title = ?",
            $translit);
         if(empty($f)) break;
         $n++;
         $translit = Main::translit($title_ru).'-'.$n;
      }

      $this->insert($this->table, [
         'title' => $translit,
      ]);
      $article_id = $this->lastInsertId();
      $this->insert($this->table_texts, [
         'lang' => 'ru',
         'article_id' => $article_id,
         'title' => $title_ru,
      ]);
   }

   /**
    * @param $session
    *
    * @return array
    */
   public function getAll($session){
      $page = (int)$session->page;
      $on_page = (int)$session->on_page;
      $where = "";
      $res = $this->forPagingData2($page, $on_page, $this->table, $where);
      if(empty($res['count'])) return $res;

      $res['data'] = $this->fetchAll("SELECT * FROM $this->table $where
         LIMIT ?, ?", [($page - 1) * $on_page, $on_page]);
      return $res;
   }

   /**
    * @param $id
    */
   public function delArticle($id){
      $id = (int)$id;
      $this->delete($this->table, "id = $id");
      $this->delete($this->table_texts, "article_id = $id");
   }

   /**
    * @param $id
    *
    * @return array|bool
    */
   public function getById($id){
      $id = (int)$id;
      return $this->fetchRow("SELECT * FROM $this->table WHERE id = ?", $id);
   }

   /**
    * @param $id
    * @param $lang
    *
    * @return array|bool
    */
   public function getByIdAndLang($id, $lang){
      $id = (int)$id;
      return $this->fetchRow("SELECT * FROM $this->table t
         LEFT JOIN $this->table_texts tt ON tt.article_id = t.id AND tt.lang = ?
         WHERE t.id = ?", [$lang, $id]);
   }

   /**
    * @param $id
    */
   public function changeActive($id){
      $term = $this->getById($id);
      if(empty($term)) return;

      $this->update($this->table, [
         'active' => $term['active'] == 'yes'? 'no': 'yes',
      ], "id = $id");
   }

   /**
    * @param $params
    * @param $article_id
    * @param $lang
    *
    * @throws \Exception
    */
   public function editArticleTexts($params, $article_id, $lang){
      if(empty($params['title'])) throw new \Exception('Пустое название');

      $texts = $this->fetchRow("SELECT * FROM $this->table_texts
         WHERE article_id = ? AND lang = ?", [$article_id, $lang]);
      $data = [
         'article_id' => $article_id,
         'lang' => $lang,
         'title' => $params['title'],
         'short_text' => $params['short_text'],
         'text' => $params['text'],
         'meta_title' => $params['meta_title'],
         'meta_keywords' => $params['meta_keywords'],
         'meta_description' => $params['meta_description'],
      ];
      if(empty($texts)) $this->insert($this->table_texts, $data);
      else $this->update($this->table_texts, $data, "id = {$texts['id']}");
   }
   
   public function getArticlesByTerms($term_id, $lang){
      return $this->fetchAll("SELECT tt.*, 
         (SELECT title FROM $this->table WHERE id = tt.article_id) t_title 
         FROM $this->table_texts tt
         WHERE lang = ? AND article_id IN
            (SELECT article_id FROM article2term WHERE term_id = ?)",
            [$lang, $term_id]);
   }

}
<?php
namespace Application\Model;



class SectionTexts extends Base{

   private $table = 'section_texts';

   public function getBySectionIdAndLang($section_id, $lang){
      return $this->fetchRow("SELECT * FROM $this->table WHERE section_id = ?
         AND lang = ?", [$section_id, $lang]);
   }

   public function editSection($params){
      unset($params['act']);
      unset($params['submit']);
      $text = $this->getBySectionIdAndLang($params['section_id'],
         $params['lang']);
      if(empty($text)) $this->insert($this->table, $params);
      else $this->update($this->table, $params, "id = {$text['id']}");
   }

   
}
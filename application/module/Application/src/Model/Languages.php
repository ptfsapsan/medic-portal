<?php
namespace Application\Model;

class Languages extends Base{

   private $table = 'languages';

   public function addLanguage($params){
      $this->insert($this->table, [
         'code' => $params['code'],
         'title' => $params['title'],
      ]);
   }

   public function deleteLanguage($id){
      $id = (int)$id;
      $this->delete($this->table, "id = $id");
   }

   public function getAll(){
      return $this->fetchAll("SELECT * FROM $this->table");
   }

   public function getAllActive(){
      return $this->fetchAll("SELECT * FROM $this->table WHERE active = 'yes'");
   }
   
   public function getCodes(){
      $langs = $this->getAllActive();
      return array_column($langs, 'code');
   }
   
   public function isCode($code){
      return (bool) $this->fetchRow("SELECT * FROM $this->table
         WHERE code = ?", $code);
   }

   public function getCurLang(){
      return $this->getTranslator()->getLocale();
   }

   public function getById($id){
      $id = (int)$id;
      return $this->fetchRow("SELECT * FROM $this->table WHERE id = ?", $id);
   }

   public function changeActive($id){
      $lang = $this->getById($id);
      if(empty($lang)) return;

      $this->update($this->table, [
         'active' => $lang['active'] == 'yes'? 'no': 'yes',
      ], "id = $id");
   }
   
}
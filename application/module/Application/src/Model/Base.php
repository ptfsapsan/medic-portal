<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceManager;

abstract class Base{

   protected $_sm;
   protected $_db;
   private $_sql;

   public function __construct(ServiceManager $sm){
      $this->_sm = $sm;
      $db = $sm->get('dbAdapter');
      $this->_db = $db;
      $this->_sql = new Sql($db);
   }

   protected function getSm(){
      return $this->_sm;
   }

   public function getTranslator(){
      return $this->_sm->get('translator');
   }
   
   public function getCurLang(){
      return $this->getTranslator()->getLocale();
   }

   public function getConfig(){
      return $this->_sm->get('config');
   }

   protected function getQuery($sql_obj){
      $db = $this->_db;
      $query = $this->_sql->buildSqlString($sql_obj);
      $res = $db->query($query, $db::QUERY_MODE_EXECUTE);
      if(!$res) throw new \Exception("Error sql");
      return $res;
   }

   protected function fetchAll($sql_string, $params = []){
      $params = (array)$params;
      $res = [];
      $r = $this->_db->query($sql_string, $params);
      foreach($r as $i){
         $res[] = (array)$i;
      }
      return $res;
   }

   protected function fetchRow($sql_string, $params = []){
      $params = (array)$params;
      $r = (array)$this->_db->query($sql_string, $params)
         ->current();
      $res = isset($r[0]) && !$r[0]? false: $r;
      return $res;
   }

   protected function fetchOne($sql_string, $params = []){
      $params = (array)$params;
      $r = $this->fetchRow($sql_string, (array)$params);
      $res = empty($r)? false: current($r);
      return $res;
   }

   protected function insert($table_name, $key_values){
      $insert = $this->_sql->insert($table_name);
      $insert->columns(array_keys($key_values));
      $insert->values($key_values);
      $this->getQuery($insert);
   }

   protected function lastInsertId(){
      return $this->_db->getDriver()
         ->getLastGeneratedValue();
   }

   protected function update($table_name, $key_values, $where){
      $update = $this->_sql->update($table_name);
      $update->set($key_values);
      $update->where($where);
      $this->getQuery($update);
   }

   protected function delete($table_name, $where){
      $delete = $this->_sql->delete($table_name);
      $delete->where($where);
      $this->getQuery($delete);
   }

   protected static function encodeDate($date){
      list($d, $m, $Y) = explode('.', $date);
      if(!checkdate($m, $d, $Y)) throw new \Exception('Неверная дата');
      return "$Y-$m-$d";
   }

   protected static function decodeDate($date){
      list($Y, $m, $d) = explode('-', $date);
      if(!checkdate($m, $d, $Y)) throw new \Exception('Неверная дата');
      return "$d.$m.$Y";
   }

   public function quoteValue($value){
      return $this->_db->platform->quoteValue($value);
   }

   protected static function forPagingData($session, $count){
      $page = (int)$session->page;
      $on_page = (int)$session->on_page;
      $max_page = ceil($count / $on_page);
      $page = $page > $max_page? $page = $max_page: ($page < 1? 1: $page);
      $res = [
         'data' => [],
         'count' => $count,
         'page' => $page,
         'on_page' => $on_page,
      ];
      return $res;
   }

   protected function forPagingData2($page, $on_page, $table, $where=null){
      $count = $this->fetchOne("SELECT COUNT(*) FROM $table $where");
      $page = (int)$page;
      $on_page = (int)$on_page;
      $max_page = ceil($count / $on_page);
      $page = $page > $max_page? $page = $max_page: ($page < 1? 1: $page);
      $res = [
         'data' => [],
         'count' => $count,
         'page' => $page,
         'on_page' => $on_page,
      ];
      return $res;
   }

}
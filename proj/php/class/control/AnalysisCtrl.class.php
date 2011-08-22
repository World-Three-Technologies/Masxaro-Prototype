<?php

class AnalysisCtrl extends Ctrl{
	
  public function getTagData($acc,$date){
    $account = addslashes($acc);
    $date = addslashes($date);
    $sql = "
      SELECT rt.tag as category,sum(r.total_cost) as value 
      FROM receipt AS r 
      JOIN receipt_tag AS rt ON rt.receipt_id = r.id 
      WHERE r.user_account = '$account' AND r.total_cost >0 AND rt.tag != '' 
      GROUP BY rt.tag
    ";

    $this->db->select($sql);

		return $this->db->fetchAssoc();
  }

  public function getStoreData($acc,$date){
    $account = addslashes($acc);
    $date = addslashes($date);
    $sql = "
      SELECT store_account as category,sum(total_cost) as value 
      FROM receipt 
      WHERE user_account = '$account' AND total_cost >0 AND store_account != ''
      GROUP BY store_account
    ";

    $this->db->select($sql);

		return $this->db->fetchAssoc();
  }
}
?>

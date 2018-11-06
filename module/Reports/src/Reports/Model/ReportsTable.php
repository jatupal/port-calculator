<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Reports\Model;

 use Zend\Db\TableGateway\TableGateway;

 class ReportsTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }
	 
	 public function getTotalToday(){
         $adapter = $this->tableGateway->getAdapter();         
         $sql = 'SELECT created_at AS "created_at", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE DATE(created_at) = CURDATE()';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
	 }
         
          public function getTotalWeek(){
         $adapter = $this->tableGateway->getAdapter();         
         $sql = 'SELECT created_at AS "created_at", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE DATE(created_at) > CURDATE() - 7 ';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
	 }
	 
	  public function getTotalMonth(){
         $adapter = $this->tableGateway->getAdapter();         
         $sql = 'SELECT created_at AS "created_at", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE DATE(created_at) >= DATE_ADD(CURDATE(), INTERVAL -31 DAY) AND  DATE(created_at) <= DATE(NOW()) ';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
	 }
         
          public function getTotalYear(){
         $adapter = $this->tableGateway->getAdapter();         
         $sql = 'SELECT created_at AS "created_at", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE DATE(created_at) > CURDATE() - 365 ';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
	 }
	 
	      public function getTotalYearAjax($chartId=''){
                  $adapter = $this->tableGateway->getAdapter();  
                  if($chartId == 'chart2'){
                      $sql = 'SELECT MONTHNAME(created_at) AS "month", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE YEAR(created_at) = YEAR(NOW()) GROUP BY MONTH(created_at)';
                        $resultSetCur = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                        $sql = 'SELECT MONTHNAME(created_at) AS "month", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE YEAR(created_at) = YEAR(NOW())-1 GROUP BY MONTH(created_at)';
                        $resultSetPrev = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                        $curArray = $resultSetCur->toArray();
                        $prevArray = $resultSetPrev->toArray();
                        $monthdata = array(array('Month', '2016', '2017'));
                        foreach($curArray as $key=>$val){
                            $monthdata[$key+1][]=$val['month'];
                            $monthdata[$key+1][]= (isset($prevArray[$key]['total']))? $prevArray[$key]['total']: '0' ;
                            $monthdata[$key+1][]=$val['total'];
                        }
                        
                      $resultSet = $monthdata;
                  }else{
                
         $sql = 'SELECT MONTHNAME(created_at) AS "month", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE YEAR(created_at) = YEAR(NOW()) GROUP BY MONTH(created_at)';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                  }
		 return  $resultSet;
	 }
         
              public function getTotalWeekAjax($chartId='',$store='',$category=''){
          $adapter = $this->tableGateway->getAdapter();
         $where = 'created_at >= DATE_ADD(CURDATE(), INTERVAL -84 DAY)';
         if(isset($store) && $store!='' && $store!='xxxx') $where.= " AND o.store_id = '$store' ";
         if(isset($category) && $category!='' && $category!='xxxx') $where.= " AND cc.entity_id = '$category' ";         
                  
        // $sql = 'SELECT WEEK(created_at) AS "month", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE created_at >= DATE_ADD(CURDATE(), INTERVAL -84 DAY)  GROUP BY WEEK(created_at) ';
        $sql = 'SELECT WEEK(l.created_at) AS "month", SUM(l.grand_total_thb) AS "total", o.store_id , cc.category_name FROM order_line_item l LEFT JOIN orders o on l.order_id = o.order_id'
                 . ' AND o.order_status <> 4 LEFT JOIN catalog_category_products cp ON cp.product_id = l.product_id LEFT JOIN catalog_category_entities cc ON cp.category_id = cc.entity_id WHERE '.$where.' GROUP BY WEEK(l.created_at)';
        $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE); 
        
        
        
        if($chartId == 'chart2'){
                      $sql = 'SELECT MONTHNAME(created_at) AS "month", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE YEAR(created_at) = YEAR(NOW()) GROUP BY MONTH(created_at)';
                        $resultSetCur = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                        $curArray = $resultSetCur->toArray();
                        $prevArray = $resultSetPrev->toArray();
                        $monthdata = array(array('Month', '2016', '2017'));
                        foreach($curArray as $key=>$val){
                            $monthdata[$key+1][]=$val['month'];
                            $monthdata[$key+1][]= (isset($prevArray[$key]['total']))? $prevArray[$key]['total']: '0' ;
                            $monthdata[$key+1][]=$val['total'];
                        }
                        
                      $resultSet = $monthdata;
                  }
        
		 return  $resultSet;
	 }
         
         public function getTotalDaysAjax($chartId='',$store='',$category='' ){
         $adapter = $this->tableGateway->getAdapter();
         $where = 'l.created_at >= DATE_ADD(CURDATE(), INTERVAL -31 DAY) ';
         if(isset($store) && $store!='' && $store!='xxxx') $where.= " AND o.store_id = '$store' ";
         if(isset($category) && $category!='' && $category!='xxxx'){
              if($category == 'NULL') {
                  
              }
             $where.= " AND cc.entity_id = '$category' ";   
            
         }
         $sql = 'SELECT DATE(l.created_at) AS "month", SUM(l.grand_total_thb) AS "total", o.store_id , cc.category_name FROM order_line_item l LEFT JOIN orders o on l.order_id = o.order_id'
                 . ' AND o.order_status <> 4 LEFT JOIN catalog_category_products cp ON cp.product_id = l.product_id LEFT JOIN catalog_category_entities cc ON cp.category_id = cc.entity_id WHERE '.$where.' GROUP BY DATE(l.created_at)';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
	  return  $resultSet;
	 }
         
         
         public function getTotalAjax($period, $chartId='',$store='',$category='', $start_date='', $end_date='', $piedevider=''){
              $adapter = $this->tableGateway->getAdapter();
              $log['message'] = 'success';
              $log['compare']  = 0;
              $orderby = '';
              $return = array();
             switch($period){
                 case 'days':
                     $prd = 'DATE';
                     $where = 'DATE(l.created_at) >= DATE_ADD(CURDATE(), INTERVAL -31 DAY) AND  DATE(l.created_at) <= DATE(NOW()) ';
                     $where2 = 'DATE(l.created_at) <= DATE_ADD(CURDATE(), INTERVAL -1 YEAR) AND l.created_at >= DATE_ADD(DATE_ADD(CURDATE(), INTERVAL -1 YEAR),INTERVAL -31 DAY) ';
                     break;
                 case 'week':
                     $prd = 'WEEK';
                     $mw = "Week";
                     $where = 'DATE(l.created_at) >= DATE_ADD(CURDATE(), INTERVAL -84 DAY) AND  DATE(l.created_at) <= DATE(NOW()) ';
                     $where2 = 'DATE(l.created_at) <= DATE_ADD(CURDATE(), INTERVAL -1 YEAR) AND l.created_at >= DATE_ADD(DATE_ADD(CURDATE(), INTERVAL -1 YEAR),INTERVAL -84 DAY) ';
                     break;
                 case 'month':
                     $prd = 'MONTHNAME';
                      $mw = "Month";
                     $where = 'DATE(l.created_at) > DATE_ADD(CURDATE(), INTERVAL -1 YEAR)';
                     $where2 = 'DATE(l.created_at) >= DATE_ADD(CURDATE(), INTERVAL -2 YEAR) AND DATE(l.created_at) <= DATE_ADD(CURDATE(), INTERVAL -1 YEAR)';
                    if($chartId == 'chart2'){
                         $where = 'YEAR(l.created_at) = YEAR(NOW())';
                         $where2 = 'YEAR(l.created_at) = YEAR(NOW()) -1';
                    }
                     
                     $orderby = 'ORDER BY DATE(l.created_at) ASC';
                     break;
                 case 'special':
                 case 'pie':
                     $prd = 'DATE';
                     $where = ' DATE(l.created_at) >= DATE("'.$start_date.'") AND  DATE(l.created_at) <= DATE("'.$end_date.'") ';
                     break;
             }
             
              if(isset($store) && $store!='' && $store!='xxxx') $where.= " AND o.store_id = '$store' ";
              if(isset($category) && $category!='' && $category!='xxxx'){
                  if($category == 'NULL') {
                   $where.= " AND cc.entity_id IS NULL OR cc.entity_id=1"; 
              }else{
                  
                  $childs = $this->getChildCat($category);
                  $childsval = $childs->toArray();
                  if(!$childsval[0]['childs']){
                      $childsval[0]['childs'] = $category;
                  }
                  $where.= " AND cc.entity_id IN ({$childsval[0]['childs']}) "; 
              }
              }
             $sql = 'SELECT '.$prd.'(l.created_at) AS "month", SUM(l.grand_total_thb) AS "total", o.store_id , cc.category_name FROM order_line_item l LEFT JOIN orders o on l.order_id = o.order_id'
                 . ' AND o.order_status <> 4 LEFT JOIN catalog_category_products cp ON cp.product_id = l.product_id LEFT JOIN catalog_category_entities cc ON cp.category_id = cc.entity_id WHERE '.$where.' GROUP BY '.$prd.'(l.created_at) '.$orderby;
            
             $log['sql'] = $sql;
             $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
             
             switch ($chartId){
              
                 case 'chart4':
                    
                  
                     if($piedevider == 'category'){
                         $parentArr = array();
                        $resultSet = array(); 
                          //$where .= ' AND cc.category_name <> ""';
                               $sql = 'SELECT  cc.entity_id as id,  cc.category_name   AS "month", SUM(l.grand_total_thb) AS "total", o.store_id , cc.category_name FROM order_line_item l LEFT JOIN orders o on l.order_id = o.order_id'
                 . ' AND o.order_status <> 4 LEFT JOIN catalog_category_products cp ON cp.product_id = l.product_id LEFT JOIN catalog_category_entities cc ON cp.category_id = cc.entity_id WHERE '.$where.' GROUP BY cc.category_name ';
                     $resultSetChilds = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                     $sql2 = 'SELECT  cc.entity_id as id,  cc.category_name   AS "month", SUM(l.grand_total_thb) AS "total", o.store_id , cc.category_name FROM order_line_item l LEFT JOIN orders o on l.order_id = o.order_id'
                 . ' AND o.order_status <> 4 LEFT JOIN catalog_category_products cp ON cp.product_id = l.product_id LEFT JOIN catalog_category_entities cc ON cp.category_id = cc.entity_id WHERE '.$where.' GROUP BY cc.category_name';
                     $resultSetChilds2 = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE); 
                     
                      $count = 0;
                     foreach($resultSetChilds2 as $resultSetChild2){
                         $log['childs'][] = $resultSetChild2;
                         if(empty($resultSetChild2->id)) $count = 1;
                     }
                     foreach($resultSetChilds as $key=>$resultSetChild){
                         $praent = $this->getParentBychild($resultSetChild->id);
                         $parentVal = $praent->toArray();
                        
                         
                    if(isset($parentVal[0])){     
                         if($resultSetChild->id == $parentVal[0]['entity_id']){
                             $resultSet[$count]['month'] = $parentVal[0]['category_name'];
                             $resultSet[$count]['total'] = $resultSetChild->total;
                             $parentArr[$parentVal[0]['entity_id']] = $count;
                             $count ++;
                         }else{
                             
                             if(array_key_exists($parentVal[0]['entity_id'], $parentArr)){
                                // $log['par'][] = $parentArr;
                                 $val =$resultSet[$parentArr[$parentVal[0]['entity_id']]]['total'] ;
                                  $log['val'][] = $val;
                                 $resultSet[$parentArr[$parentVal[0]['entity_id']]]['total'] = $val + $resultSetChild->total;
                            
                             }else {
                                $resultSet[$count]['month'] = $parentVal[0]['category_name'];
                                $resultSet[$count]['total'] = $resultSetChild->total; 
                                $parentArr[$parentVal[0]['entity_id']] = $count;
                                $count ++;
                             }
                             
                         }
                    }else{
                        // add default
                        $resultSet[0]['month'] = 'Default';
                        $resultSet[0]['total'] = $resultSetChild->total;
                    }
                        $log['query'] =  $sql;
                        $log['result'] =  $resultSet;
                        
                 
                     }
                               
                     }else{
                           // $where .= ' AND o.store_id <> "" AND o.store_id <> 0';
                     $sql = 'SELECT o.store_name  AS "month", SUM(l.grand_total_thb) AS "total", o.store_id , cc.category_name FROM order_line_item l LEFT JOIN orders o on l.order_id = o.order_id'
                 . ' AND o.order_status <> 4 LEFT JOIN catalog_category_products cp ON cp.product_id = l.product_id LEFT JOIN catalog_category_entities cc ON cp.category_id = cc.entity_id WHERE '.$where.' GROUP BY o.store_id '.$orderby;
                     $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                     }
             
                     
                  // $log['sql'] =   $sql;
                     break;
                     
                
            case 'chart2': 
             
             
                $sql2 = 'SELECT '.$prd.'(l.created_at) AS "month", SUM(l.grand_total_thb) AS "total", o.store_id , cc.category_name FROM order_line_item l LEFT JOIN orders o on l.order_id = o.order_id'
                 . ' AND o.order_status <> 4 LEFT JOIN catalog_category_products cp ON cp.product_id = l.product_id LEFT JOIN catalog_category_entities cc ON cp.category_id = cc.entity_id WHERE '.$where2.' GROUP BY '.$prd.'(l.created_at) '.$orderby;
                 $resultSetPrev = $adapter->query($sql2, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
                 $curArray = $resultSet->toArray();
                 $prevArray = $resultSetPrev->toArray();
                 $log['carray'] = $curArray;
                  $log['parray'] = $prevArray;
                 if(!empty($prevArray)){
                     $log['compare']  = 1;
                     $monthdata = array(array($mw, '2016', '2017'));
                     
                     
                     if($period == 'month'){
                        $months = array('January', 'February', 'March', 'Apri', 'May', 'June', 'July', 'August', 'September','October', 'November', 'December');
                        foreach ($months as $key => $month){
                            $monthdata[$key+1][] = $month;
                            $monthmatch = 0;
                                foreach($prevArray as $key2=>$pval){
                                if($pval['month'] == $month){
                                    $monthdata[$key+1][]= floatval($prevArray[$key2]['total']);
                                    $monthmatch = 1;
                                }
                            }
                            if(!$monthmatch)  $monthdata[$key+1][] = 0;
                            
                            $monthmatch = 0;
                                foreach($curArray as $key3=>$cval){
                                if($cval['month'] == $month && $cval['total']){
                                    $monthdata[$key+1][]= floatval($cval['total']);
                                    $monthmatch = 1;
                                }
                            }
                            
                            
                            if(!$monthmatch)  $monthdata[$key+1][] = 0;
                            
                            }
                     }else {
                         $monthdata = array(array($mw, '2017', '2016'));                        
                        foreach($curArray as $key=>$val){
                            $monthmatch = 0;
                            $monthdata[$key+1][]=$val['month'];
                            $monthdata[$key+1][]= floatval($val['total']);
                            foreach($prevArray as $key2=>$pval){
                                if($val['month'] == $pval['month']){
                                    $monthdata[$key+1][]= floatval($prevArray[$key2]['total']);
                                    $monthmatch = 1;
                                }
                            }
                            
                            if(!$monthmatch)  $monthdata[$key+1][] = 0;
                            
                        }
                     }
                     
                        
                 }else{
                    $monthdata = array(array($mw, '2017'));                        
                        foreach($curArray as $key=>$val){
                            $monthdata[$key+1][]=$val['month'];
                            $monthdata[$key+1][]= floatval($val['total']);
                           // $monthdata[$key+1][]= (isset($prevArray[$key]['total']))? round($prevArray[$key]['total']): '0' ;
                        } 
                 }
                      $resultSet = $monthdata;
                
                break;
        }
        
        $return['log'] = $log;
        $return['data'] = $resultSet;
        
	  return  $return;
             
             
             
        }
        
        public function getParentBychild($child){
            $adapter = $this->tableGateway->getAdapter(); 
            $sql = "SELECT f.entity_id, f.category_name FROM ( SELECT @id AS _id, (SELECT @id := parent_id FROM catalog_category_entities WHERE entity_id = _id) "
                    . "FROM (SELECT @id := '$child') tmp1 JOIN catalog_category_entities ON @id <> 1 ) tmp2 JOIN catalog_category_entities f ON tmp2._id = f.entity_id WHERE f.parent_id = 1";
            $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
            
        }
         public function getWidgets(){
         $adapter = $this->tableGateway->getAdapter();         
         $sql = 'SELECT * FROM `dhashboard_widgets` WHERE `status` = 1 ORDER BY `weight` ASC';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
	 }
         
         public function getStores(){
         $adapter = $this->tableGateway->getAdapter();         
         $sql = 'SELECT DISTINCT store_id, store_name from orders ';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
	 $selectlist = "<select class='shStores pull-right'><option value='xxxx'>Stores</option>";
         foreach($resultSet as $result){
             $store_name = ($result->store_name) ? $result->store_name : 'Default';
             $selectlist.= "<option value='{$result->store_id}'> $store_name</option>";
         }         
         $selectlist.= "</select>";
         return  $selectlist;
	 }
         
          public function getProductCategories(){
         $adapter = $this->tableGateway->getAdapter();         
         $sql = 'SELECT entity_id, category_name FROM `catalog_category_entities` WHERE parent_id = 1';
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
         $selectlist = "<select class='shCategories pull-right'> "
                 . "<option value='xxxx'>Product Categories</option><option value='NULL'>Default Categories</option>";
         foreach($resultSet as $result){
             $selectlist.= "<option value='{$result->entity_id}'> {$result->category_name}</option>";
         }         
         $selectlist.= "</select>";
         
         
         return  $selectlist;
         
         
		 return  $resultSet;
	 }
         
         public function getChildCat($cat){
              $adapter = $this->tableGateway->getAdapter();  
             $sql = "SELECT GROUP_CONCAT(lv SEPARATOR ',') as childs FROM (
                    SELECT @pv:=(SELECT GROUP_CONCAT(entity_id SEPARATOR ',') FROM catalog_category_entities WHERE parent_id IN (@pv)) AS lv FROM catalog_category_entities
                    JOIN
                    (SELECT @pv:=$cat)tmp
                    WHERE parent_id IN (@pv)) a";
             $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
             
         }
         
         public function undateWidgets($id, $weight){
              $adapter = $this->tableGateway->getAdapter();         
         $sql = "UPDATE `dhashboard_widgets` SET `weight` = '$weight' WHERE `dhashboard_widgets`.`id` = $id;";
         $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
		 return  $resultSet;
         }
     
     public function getTotals(){
     //    $adapter = $this->tableGateway->getAdapter();
         
    //     $sql = 'SELECT MONTH(created_at) AS "created_at", SUM(grand_total_thb) AS "total" FROM order_line_item WHERE MONTH(created_at) = MONTH(NOW()) //GROUP BY MONTH(created_at)';
      //   $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
         
//         $sql = new Sql($dbAdapter);
//$select = $sql->select();
//$select->from('testTable');
//$select->where(array('myColumn' => 5));
//
//$statement = $sql->prepareStatementForSqlObject($select);
//$result = $statement->execute();
          $sql = $this->tableGateway->getSql();

    // We'll follow the regular order of SQL ( SELECT, FROM, WHERE )
    // So the query is easier to understand
    $select = $sql->select()
            // Use an alias as key in the columns array instead of
            // in the expression itself
            ->columns(array('created_at' => new \Zend\Db\Sql\Expression('MONTH(created_at)'),'total' => new \Zend\Db\Sql\Expression('SUM(grand_total_thb)')));
           
            // Type casting the variables as integer can take place
            // here ( it even tells us a little about the table structure )
           // ->where(array('MONTH(time)' => new \Zend\Db\Sql\Expression(' MONTH(NOW())'), 'project_id' => (int)$project_id));
    
	  
	  $select->where(array('created_at' =>new \Zend\Db\Sql\Expression('NOW()')));
	  $select->group('created_at');
	   echo $select->getSqlString(); 
    // Use selectWith as a shortcut to get a resultSet for the above select
    return $this->tableGateway->selectWith($select);
   
  // return $resultSet;
        
     }
     
       public function fetchJoin() 
    { 
        $select = new \Zend\Db\Sql\Select ; 
        $select->from('province'); 
        $select->columns(array('province')); 
        $select->join('village', "village.id_province = province.province.id", array('village'), 'left'); 
          
        echo $select->getSqlString(); 
        $resultSet = $this->tableGateway->selectWith($select); 
        
        return $resultSet; 
    }

     public function getAlbum($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveAlbum(Album $album)
     {
         $data = array(
             'artist' => $album->artist,
             'title'  => $album->title,
         );

         $id = (int) $album->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getAlbum($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Album id does not exist');
             }
         }
     }

     public function deleteAlbum($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Reports\Model;

 class Reports
 {
     public $order_item_id;
     public $company_id;
     public $order_id;
      public $total;
      public $created_at;

     public function exchangeArray($data)
     {
         
         $this->order_item_id     = (!empty($data['order_item_id'])) ? $data['order_item_id'] : null;
         $this->company_id = (!empty($data['company_id'])) ? $data['company_id'] : null;
         $this->order_id  = (!empty($data['order_id'])) ? $data['order_id'] : null;
         $this->total  = (!empty($data['total'])) ? $data['total'] : null;
         $this->created_at  = (!empty($data['created_at'])) ? $data['created_at'] : null;
     }
 }
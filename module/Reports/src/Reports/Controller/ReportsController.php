<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Reports for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Reports\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class ReportsController extends AbstractActionController
{
    protected $user_id;
    protected $name;
    protected $company_id;
    protected $Language;
    
    protected $baseUrl;
    
    public function getBaseUrl()
	{
        $basePath = $this->getRequest()->getBasePath();
        $uri = new \Zend\Uri\Uri($this->getRequest()->getUri());
        $uri->setPath($basePath);
        $uri->setQuery(array());
        $uri->setFragment('');
        $this->baseUrl = $uri->getPath();
        
        return $this->baseUrl;
    } 
    
    public function __construct()
	{
        $session = new Container('User');        
        
        $this->user_id = $session->offsetGet('user_id');
        $this->name = $session->offsetGet('name');

		$SearchLang = new Container('language_chg');
		$this->Language =  $SearchLang->offsetGet('ChgLanguage');
		
    }
	
	public function onDispatch(MvcEvent $e)
	{
		$response = $e->getResponse();
		$session = new Container('User');
		
		return parent::onDispatch($e);
	}
	
	public function indexAction()
    {
		$ReportSession = new Container('ReportSession');
		$PortTable = $this->getServiceLocator()->get('PortTable');
		$IndexTable = $this->getServiceLocator()->get('IndexTable');
		$SymbolTable = $this->getServiceLocator()->get('SymbolTable');
		$FuturePlanTable = $this->getServiceLocator()->get('FuturePlanTable');
		$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		//$FuturePlanTable = new Container('FuturePlanTable');
		//$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		$clear_sec = $this->params()->fromRoute('clear_sec');
        if($clear_sec != ''){
            $ReportSession->getManager()->getStorage()->clear('ReportSession');  
			$ReportSession->data = array('mode'=>'start_plan');
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
            exit();
        }
		
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
        }else{
			if(isset($ReportSession->data)){
				$data = $ReportSession->data;
			}else{
				$data = array();
				$data['show_port1'] = 'yes';
				$data['show_port2'] = 'yes';
				$data['show_summary'] = 'yes';
			}			    
        }		
		if(!isset($data['port_id'])){$data['port_id'] = '';}
		if(!isset($data['port_id2'])){$data['port_id2'] = '';}
		if(!isset($data['index_id'])){$data['index_id'] = '';}
		if(!isset($data['date_from'])){$data['date_from'] = '';}
		if(!isset($data['date_to'])){$data['date_to'] = '';}
		if(!isset($data['future_contact'])){$data['future_contact'] = '';}
		if(!isset($data['option_contact'])){$data['option_contact'] = '';}
		if(!isset($data['current_price'])){$data['current_price'] = '1000';}
		if(!isset($data['range_from'])){$data['range_from'] = '900';}
		if(!isset($data['range_to'])){$data['range_to'] = '1300';}
		if(!isset($data['offset'])){$data['offset'] = '1';}
		
		if(!isset($data['show_port1']) and !isset($data['show_port2']) and !isset($data['show_summary'])){
			$data['show_port1'] = 'yes';
			$data['show_port2'] = 'yes';
			$data['show_summary'] = 'yes';
		}
		//var_dump($data);
		
		$complete_message = '';
        $error_message = '';
		
		
		if(isset($ReportSession->complete_message) and $ReportSession->complete_message != ''){
			$complete_message = $ReportSession->complete_message;
			$ReportSession->complete_message = '';
		}
		
		if(isset($ReportSession->error_message) and $ReportSession->error_message != ''){
			$error_message = $ReportSession->error_message;
			$ReportSession->error_message = '';
		}
		
		//print_r($data); echo "<br><br>";
		$ResultPort = $PortTable->getPort('');
		$ResultIndex = $IndexTable->getIndex('');
		$ResultSymbol = $SymbolTable->getSymbol('', $data['index_id']);
		
		if(isset($ReportSession->ResultFuturePlan)){
			$ResultFuturePlan = $ReportSession->ResultFuturePlan;
		}else{
			$ResultFuturePlan = array();
		}
		
		if(isset($ReportSession->ResultOptionPlan)){
			$ResultOptionPlan = $ReportSession->ResultOptionPlan;
		}else{
			$ResultOptionPlan = array();
		}
		
		if(isset($ReportSession->ResultFuturePlan2)){
			$ResultFuturePlan2 = $ReportSession->ResultFuturePlan2;
		}else{
			$ResultFuturePlan2 = array();
		}
		
		if(isset($ReportSession->ResultOptionPlan2)){
			$ResultOptionPlan2 = $ReportSession->ResultOptionPlan2;
		}else{
			$ResultOptionPlan2 = array();
		}
		
		$mode = $this->getRequest()->getPost('mode');
		//echo 'mode => '.$mode.'<br/>'; 
		if($mode == 'add_port'){
			if($data['port_name'] != ''){
				$AddPort = array();
				$AddPort['port_name'] = $data['port_name'];
				$data['port_id'] = $PortTable->AddPort($AddPort);
				$ReportSession->complete_message = 'Add Port Complete';
				$data['mode'] == 'start_plan';
				$ReportSession->data = $data;
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
			}
		}
		
		if($mode == 'add_index'){
			if($data['index_name'] != ''){
				$AddIndex = array();
				$AddIndex['index_name'] = $data['index_name'];
				$AddIndex['future_contact'] = $data['new_future_contact'];
				$AddIndex['option_contact'] = $data['new_option_contact'];
				$data['index_id'] = $IndexTable->AddIndex($AddIndex);
				$data['future_contact'] = $data['new_future_contact'];
				$data['option_contact'] = $data['new_option_contact'];
			
				$ReportSession->complete_message = 'Add Index Complete';
				$data['mode'] == 'start_plan';
				$ReportSession->data = $data;
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
			}
		}
		
		if((isset($data['mode']) and $data['mode'] == 'start_plan') || $mode == 'change_value'){
			
			if(isset($data['port_id']) and $data['port_id'] != ''){
				$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			}else{
				$ResultFuturePlan = array();
				$ResultFuturePlan = array();
			}
			
			if(isset($data['port_id2']) and $data['port_id2'] != ''){
				$ResultFuturePlan2 = $FuturePlanTable->getFuturePlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan2 = $OptionPlanTable->getOptionPlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
			}else{
				$ResultFuturePlan2 = array();
				$ResultOptionPlan2 = array();
			}			

			$data['mode'] = '';
		}
		
		if($mode == 'change_port'){
			if(isset($data['port_id']) and $data['port_id'] != ''){
				$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			}else{
				$ResultFuturePlan = array();
				$ResultFuturePlan = array();
			}
		}
		
		
		if(isset($data['port_id2']) and $data['port_id2'] != ''){
			$ResultFuturePlan2 = $FuturePlanTable->getFuturePlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
			$ResultOptionPlan2 = $OptionPlanTable->getOptionPlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
		}else{
			$ResultFuturePlan2 = array();
			$ResultOptionPlan2 = array();
		}
		
		
		if($mode == 'add_future_plan'){
			if(isset($data['future_date']) and !empty($data['future_date'])){
				foreach($data['future_date'] as $id => $future_date){
					$UpdateFuture = array();
					$UpdateFuture['port_id'] = $data['port_id'];
					$UpdateFuture['date'] = $data['future_date'][$id];
					$UpdateFuture['symbol'] = $data['future_symbol'][$id];
					$UpdateFuture['price'] = $data['future_price'][$id];
					$UpdateFuture['amount'] = $data['future_amount'][$id];
					$ResultFuturePlan[] = $UpdateFuture;
				}
			}
		}
		
		if($mode == 'add_future_plan_2'){
			if(isset($data['future_date']) and !empty($data['future_date'])){
				foreach($data['future_date'] as $id => $future_date){
					$UpdateFuture = array();
					$UpdateFuture['port_id'] = $data['port_id'];
					$UpdateFuture['date'] = $data['future_date'][$id];
					$UpdateFuture['symbol'] = $data['future_symbol'][$id];
					$UpdateFuture['price'] = $data['future_price'][$id];
					$UpdateFuture['amount'] = $data['future_amount'][$id];
					$ResultFuturePlan2[] = $UpdateFuture;
				}
			}
		}
		
		$ReportSession->ResultFuturePlan = $ResultFuturePlan;
		$ReportSession->ResultOptionPlan = $ResultOptionPlan;
		
		$ReportSession->ResultFuturePlan2 = $ResultFuturePlan2;
		$ReportSession->ResultOptionPlan2 = $ResultOptionPlan2;
		
		$ReportSession->data = $data;
		if($mode == 'save_value' || $mode == 'changePerPage'){}
		
		if($mode == 'save_value2'){
			$port_id = $data['port_id'];
			if($port_id == ''){				
				$ResultFuturePlan = array();
				$ResultOptionPlan = array();
				
				if(isset($data['future_id']) and !empty($data['future_id'])){
					foreach($data['future_id'] as $id => $future_id){
						if($future_id == 'new'){
							$UpdateFuture = array();
							$UpdateFuture['date'] = $data['future_date'][$id];
							$UpdateFuture['transaction_id'] = $data['future_transaction_id'][$id];
							$UpdateFuture['symbol'] = $data['future_symbol'][$id];
							$UpdateFuture['price'] = $data['future_price'][$id];
							$UpdateFuture['amount'] = $data['future_amount'][$id];
							$UpdateFuture['port_id'] = $port_id;
							$FuturePlanTable->AddFuturePlan($UpdateFuture);
						}
					}
				}
				
				if(isset($data['option_id']) and !empty($data['option_id'])){
					foreach($data['option_id'] as $id => $option_id){
						if($option_id == 'new'){
							$UpdateOption = array();
							$UpdateOption['date'] = $data['option_date'][$id];
							$UpdateOption['transaction_id'] = $data['option_transaction_id'][$id];
							$UpdateOption['symbol'] = $data['option_symbol'][$id];
							$UpdateOption['type'] = $data['option_type'][$id];
							$UpdateOption['price'] = $data['option_price'][$id];
							$UpdateOption['premium'] = $data['option_premium'][$id];
							$UpdateOption['amount'] = $data['option_amount'][$id];
							$UpdateOption['port_id'] = $port_id;
							$OptionPlanTable->AddOptionPlan($UpdateOption);
						}
					}
				}
				$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			
				$ReportSession->ResultFuturePlan = $ResultFuturePlan;
				$ReportSession->ResultOptionPlan = $ResultOptionPlan;
			
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
			}
		}
		
		if($mode != '' and $mode != 'change_value'and $mode != 'change_port'){
			$port_id = $data['port_id'];
			if($port_id == ''){
				$ResultFuturePlan = array();
				$ResultOptionPlan = array();
				
				if(isset($data['future_id']) and !empty($data['future_id'])){
					foreach($data['future_id'] as $id => $future_id){
						if($future_id == 'new'){
							$UpdateFuture = array();
							$UpdateFuture['date'] = $data['future_date'][$id];
							$UpdateFuture['transaction_id'] = $data['future_transaction_id'][$id];
							$UpdateFuture['symbol'] = $data['future_symbol'][$id];
							$UpdateFuture['price'] = $data['future_price'][$id];
							$UpdateFuture['amount'] = $data['future_amount'][$id];
							$UpdateFuture['port_id'] = $port_id;
							$UpdateFuture['future_id'] = $future_id;
							$ResultFuturePlan[] = $UpdateFuture;
						}
					}
				}
				
				if(isset($data['option_id']) and !empty($data['option_id'])){
					foreach($data['option_id'] as $id => $option_id){
						if($option_id == 'new'){
							$UpdateOption = array();
							$UpdateOption['date'] = $data['option_date'][$id];
							$UpdateOption['transaction_id'] = $data['option_transaction_id'][$id];
							$UpdateOption['symbol'] = $data['option_symbol'][$id];
							$UpdateOption['type'] = $data['option_type'][$id];
							$UpdateOption['price'] = $data['option_price'][$id];
							$UpdateOption['premium'] = $data['option_premium'][$id];
							$UpdateOption['amount'] = $data['option_amount'][$id];
							$UpdateOption['port_id'] = $port_id;
							$UpdateOption['option_id'] = $option_id;
							$ResultOptionPlan[] = $UpdateOption;
						}
					}
				}
			
			}else{
			
			if(isset($data['future_id']) and !empty($data['future_id'])){
				foreach($data['future_id'] as $id => $future_id){
					if($future_id == 'new'){
						$UpdateFuture = array();
						$UpdateFuture['date'] = $data['future_date'][$id];
						$UpdateFuture['transaction_id'] = $data['future_transaction_id'][$id];
						$UpdateFuture['symbol'] = $data['future_symbol'][$id];
						$UpdateFuture['price'] = $data['future_price'][$id];
						$UpdateFuture['amount'] = $data['future_amount'][$id];
						$UpdateFuture['port_id'] = $port_id;
						$FuturePlanTable->AddFuturePlan($UpdateFuture);
					}else{
						$UpdateFuture = array();
						$UpdateFuture['date'] = $data['future_date'][$id];
						$UpdateFuture['transaction_id'] = $data['future_transaction_id'][$id];
						$UpdateFuture['symbol'] = $data['future_symbol'][$id];
						$UpdateFuture['price'] = $data['future_price'][$id];
						$UpdateFuture['amount'] = $data['future_amount'][$id];
						$FuturePlanTable->UpdateFuturePlan($UpdateFuture,$ResultFuturePlan[$id]['future_id']);
					}
				}
			}
			
			if(isset($data['option_id']) and !empty($data['option_id'])){
				foreach($data['option_id'] as $id => $option_id){
					if($option_id == 'new'){
						$UpdateOption = array();
						$UpdateOption['date'] = $data['option_date'][$id];
						$UpdateOption['transaction_id'] = $data['option_transaction_id'][$id];
						$UpdateOption['symbol'] = $data['option_symbol'][$id];
						$UpdateOption['type'] = $data['option_type'][$id];
						$UpdateOption['price'] = $data['option_price'][$id];
						$UpdateOption['premium'] = $data['option_premium'][$id];
						$UpdateOption['amount'] = $data['option_amount'][$id];
						$UpdateOption['port_id'] = $port_id;
						$OptionPlanTable->AddOptionPlan($UpdateOption);
					}else{
						$UpdateOption = array();
						$UpdateOption['date'] = $data['option_date'][$id];
						$UpdateOption['transaction_id'] = $data['option_transaction_id'][$id];
						$UpdateOption['symbol'] = $data['option_symbol'][$id];
						$UpdateOption['type'] = $data['option_type'][$id];
						$UpdateOption['price'] = $data['option_price'][$id];
						$UpdateOption['premium'] = $data['option_premium'][$id];
						$UpdateOption['amount'] = $data['option_amount'][$id];
						$OptionPlanTable->UpdateOptionPlan($UpdateOption,$ResultOptionPlan[$id]['option_id']);
					}
				}
			}
			
			if(isset($data['remove_future']) and !empty($data['remove_future'])){
				foreach($data['remove_future'] as $remove_id){
					$FuturePlanTable->ProcessDeleteFuturePlan($remove_id);
				}
			}
			
			if(isset($data['remove_option']) and !empty($data['remove_option'])){
				foreach($data['remove_option'] as $remove_id){
					$OptionPlanTable->ProcessDeleteOptionPlan($remove_id);
				}
			}
			
			$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			}
			$ReportSession->ResultFuturePlan = $ResultFuturePlan;
			$ReportSession->ResultOptionPlan = $ResultOptionPlan;
			
			//return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
		}		
		
		$SumFuture = array();
		$SumOption = array();
		$Total = array();
		
		$SumFuture2 = array();
		$SumOption2 = array();
		$Total2 = array();
		
		$TotalSum = array();
		$check_current = 0;
		$current_future_payoff = 0;
		$current_option_payoff = 0;
		
		$current_port1_payoff = 0;
		$current_port2_payoff = 0;
		$current_summary_payoff = 0;
		
		//echo 'mode => '.$mode.'<br/>';
		//if($mode == 'calculate_value'){
			//var_dump($data['range_from']);
			//var_dump($data['range_to']);
			//var_dump($data['offset']);
			if($data['range_from'] != '' and $data['range_to'] != ''){
				$time_from = strtotime($data['range_from']);
				$time_to = strtotime($data['range_to']);
				$date_from2 = $data['range_from'];
				$date_to2 = $data['range_to'];
				if($time_to > $time_from){
					$data['range_from'] = $date_from2;
					$data['range_to'] = $date_to2;
				}else{
					$data['range_to'] = $date_from2;
					$data['range_from'] = $date_to2;
				}
				if($data['range_to'] > $data['range_from'] and $data['offset'] != 0){
					$check_price = $data['range_from'];
					while($check_price <= $data['range_to']){
						if(!isset($TotalSum[$check_price])){$TotalSum[$check_price] = 0;}
						
						if(isset($ResultFuturePlan) and !empty($ResultFuturePlan)){
							foreach($ResultFuturePlan as $id => $reault_future_plan){
								$future_date = $reault_future_plan['date'];
								$future_symbol = $reault_future_plan['symbol'];
								$future_price = $reault_future_plan['price'];
								$future_amount = $reault_future_plan['amount'];
								
								$payoff = $FuturePlanTable->CalculateFuturePlan($check_price, $future_price, $future_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_future_payoff = $current_future_payoff + $payoff;
									$current_port1_payoff = $current_port1_payoff + $payoff;
									$current_summary_payoff = $current_summary_payoff + $payoff;
									$check_current++;
								}
								
								if(!isset($SumFuture[$check_price])){$SumFuture[$check_price] = 0;}
								if(!isset($Total[$check_price])){$Total[$check_price] = 0;}
								$SumFuture[$check_price] = $SumFuture[$check_price] + $payoff;
								$Total[$check_price] = $Total[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($ResultOptionPlan) and !empty($ResultOptionPlan)){ 
							foreach($ResultOptionPlan as $id => $result_option){ 
								$option_date = $result_option['date'];
								$option_symbol = $result_option['symbol'];
								$option_type = $result_option['type'];
								$option_price = $result_option['price'];
								$option_premium = $result_option['premium'];
								$option_amount = $result_option['amount'];
								$payoff = $OptionPlanTable->CalculateOptionPlan($check_price, $option_type, $option_price, $option_premium, $option_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_option_payoff = $current_option_payoff + $payoff;
									$current_port1_payoff = $current_port1_payoff + $payoff;
									$current_summary_payoff = $current_summary_payoff + $payoff;
									$check_current++;
								}
								
								if(!isset($SumOption[$check_price])){$SumOption[$check_price] = 0;}
								if(!isset($Total[$check_price])){$Total[$check_price] = 0;}
								$SumOption[$check_price] = $SumOption[$check_price] + $payoff;
								$Total[$check_price] = $Total[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($ResultFuturePlan2) and !empty($ResultFuturePlan2)){
							foreach($ResultFuturePlan2 as $id => $reault_future_plan){
								$future_date = $reault_future_plan['date'];
								$future_symbol = $reault_future_plan['symbol'];
								$future_price = $reault_future_plan['price'];
								$future_amount = $reault_future_plan['amount'];
								
								$payoff = $FuturePlanTable->CalculateFuturePlan($check_price, $future_price, $future_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_future_payoff = $current_future_payoff + $payoff;
									$current_port2_payoff = $current_port2_payoff + $payoff;
									$current_summary_payoff = $current_summary_payoff + $payoff;
									$check_current++;
								}
								if(!isset($SumFuture2[$check_price])){$SumFuture2[$check_price] = 0;}
								if(!isset($Total2[$check_price])){$Total2[$check_price] = 0;}
								$SumFuture2[$check_price] = $SumFuture2[$check_price] + $payoff;
								$Total2[$check_price] = $Total2[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($ResultOptionPlan2) and !empty($ResultOptionPlan2)){ 
							foreach($ResultOptionPlan2 as $id => $result_option){ 
								$option_date = $result_option['date'];
								$option_symbol = $result_option['symbol'];
								$option_type = $result_option['type'];
								$option_price = $result_option['price'];
								$option_premium = $result_option['premium'];
								$option_amount = $result_option['amount'];
								$payoff = $OptionPlanTable->CalculateOptionPlan($check_price, $option_type, $option_price, $option_premium, $option_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_option_payoff = $current_option_payoff + $payoff;
									$current_port2_payoff = $current_port2_payoff + $payoff;
									$current_summary_payoff = $current_summary_payoff + $payoff;
									$check_current++;
								}
								if(!isset($SumOption2[$check_price])){$SumOption2[$check_price] = 0;}
								if(!isset($Total2[$check_price])){$Total2[$check_price] = 0;}
								$SumOption2[$check_price] = $SumOption2[$check_price] + $payoff;
								$Total2[$check_price] = $Total2[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						//echo 'SumFuture => '; var_dump($SumFuture); echo '<br/><br/>';
						//echo 'SumOption => '; var_dump($SumOption); echo '<br/><br/>';
						$check_price = $check_price + $data['offset'];
						
					}
				}
			}
		//}
		
		if($data['current_price'] != '' and $check_current == 0){
			if(isset($ResultFuturePlan) and !empty($ResultFuturePlan)){
				foreach($ResultFuturePlan as $id => $reault_future_plan){
					$payoff = $FuturePlanTable->CalculateFuturePlan($data['current_price'], $future_price, $future_amount);
					$current_future_payoff = $current_future_payoff + $payoff;
				}
			}
						
			if(isset($ResultOptionPlan) and !empty($ResultOptionPlan)){ 
				foreach($ResultOptionPlan as $id => $result_option){ 
					$payoff = $OptionPlanTable->CalculateOptionPlan($data['current_price'], $option_type, $option_price, $option_premium, $option_amount);
					$current_option_payoff = $current_option_payoff + $payoff;
				}
			}
			
			if(isset($ResultFuturePlan2) and !empty($ResultFuturePlan2)){
				foreach($ResultFuturePlan2 as $id => $reault_future_plan){
					$payoff = $FuturePlanTable->CalculateFuturePlan($data['current_price'], $future_price, $future_amount);
					$current_future_payoff = $current_future_payoff + $payoff;
				}
			}
						
			if(isset($ResultOptionPlan2) and !empty($ResultOptionPlan2)){ 
				foreach($ResultOptionPlan2 as $id => $result_option){ 
					$payoff = $OptionPlanTable->CalculateOptionPlan($data['current_price'], $option_type, $option_price, $option_premium, $option_amount);
					$current_option_payoff = $current_option_payoff + $payoff;
				}
			}
		}
		
		if(!isset($data['page1'])){$data['page1'] = 1;}
		if(!isset($data['per_page1'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page1'] = $ReportSession->per_page1;
			}else{
				$data['per_page1'] = 20;
			}
		}
		if($data['per_page1'] == ''){$data['per_page1'] = 20;}
		
		if(!isset($data['page2'])){$data['page2'] = 1;}
		if(!isset($data['per_page2'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page2'] = $ReportSession->per_page1;
			}else{
				$data['per_page2'] = 20;
			}
		}
		if($data['per_page2'] == ''){$data['per_page2'] = 20;}
		
		if(!isset($data['page3'])){$data['page3'] = 1;}
		if(!isset($data['per_page3'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page3'] = $ReportSession->per_page1;
			}else{
				$data['per_page3'] = 20;
			}
		}
		if($data['per_page3'] == ''){$data['per_page3'] = 20;}
		
		if(!isset($data['page4'])){$data['page4'] = 1;}
		if(!isset($data['per_page4'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page4'] = $ReportSession->per_page1;
			}else{
				$data['per_page4'] = 20;
			}
		}
		if($data['per_page4'] == ''){$data['per_page4'] = 20;}
		
		if($mode == 'Changepage'){
			$page_name = $data['change_page_name'];
			$page_value = $data['change_page_value'];
			$data[$page_name] = $page_value;
		}
	   
		$ReportSession->per_page1 = $data['per_page1'];
        $paginator_future1 = new Paginator(new ArrayAdapter($ResultFuturePlan));
        $paginator_future1->setCurrentPageNumber($data['page1'])
        ->setItemCountPerPage($data['per_page1'])
        ;
		$future_page_slide = $this->GeneratePaginationSlide($paginator_future1, $data['page1'], $data['per_page1'], 'page1');
		
		$ReportSession->per_page2 = $data['per_page2'];
        $paginator_future2 = new Paginator(new ArrayAdapter($ResultFuturePlan2));
        $paginator_future2->setCurrentPageNumber($data['page2'])
        ->setItemCountPerPage($data['per_page2'])
        ;
		$future_page_slide2 = $this->GeneratePaginationSlide($paginator_future2, $data['page2'], $data['per_page2'], 'page2');
		
		$ReportSession->per_page3 = $data['per_page3'];
        $paginator_option1 = new Paginator(new ArrayAdapter($ResultOptionPlan));
        $paginator_option1->setCurrentPageNumber($data['page3'])
        ->setItemCountPerPage($data['per_page3'])
        ;
		$option_page_slide = $this->GeneratePaginationSlide($paginator_option1, $data['page3'], $data['per_page3'], 'page3');
		
		$ReportSession->per_page4 = $data['per_page4'];
        $paginator_option2 = new Paginator(new ArrayAdapter($ResultOptionPlan2));
        $paginator_option2->setCurrentPageNumber($data['page4'])
        ->setItemCountPerPage($data['per_page4'])
        ;
		$option_page_slide2 = $this->GeneratePaginationSlide($paginator_option2, $data['page4'], $data['per_page4'], 'page4');
		
        return new ViewModel(array(            
            'baseUrl' => $this->getBaseUrl(),
            'data' => $data,
            'ResultPort' => $ResultPort,
            'ResultIndex' => $ResultIndex,
            'ResultFuturePlan' => $ResultFuturePlan,
            'ResultFuturePlan2' => $ResultFuturePlan2,
            'ResultOptionPlan' => $ResultOptionPlan,
            'ResultOptionPlan2' => $ResultOptionPlan2,
            'complete_message' => $complete_message,
            'error_message' => $error_message,
            'paginator_future1' => $paginator_future1,
            'paginator_future2' => $paginator_future2,
            'paginator_option1' => $paginator_option1,
            'paginator_option2' => $paginator_option2,
			'lang' => $this->Language,
			'SumFuture' => $SumFuture,
			'SumOption' => $SumOption,
			'SumFuture2' => $SumFuture2,
			'SumOption2' => $SumOption2,
			'Total' => $Total,
			'Total2' => $Total2,
			'TotalSum' => $TotalSum,
			'future_page_slide' => $future_page_slide,
			'future_page_slide2' => $future_page_slide2,
			'option_page_slide' => $option_page_slide,
			'option_page_slide2' => $option_page_slide2,
			'current_future_payoff' => $current_future_payoff,
			'current_option_payoff' => $current_option_payoff,
			'current_port1_payoff' => $current_port1_payoff,
			'current_port2_payoff' => $current_port2_payoff,
			'current_summary_payoff' => $current_summary_payoff,
        )); 
		
	}
	
	public function indexOldAction()
    {
		$ReportSession = new Container('ReportSession');
		$PortTable = $this->getServiceLocator()->get('PortTable');
		$IndexTable = $this->getServiceLocator()->get('IndexTable');
		$SymbolTable = $this->getServiceLocator()->get('SymbolTable');
		$FuturePlanTable = $this->getServiceLocator()->get('FuturePlanTable');
		$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		//$FuturePlanTable = new Container('FuturePlanTable');
		//$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		$clear_sec = $this->params()->fromRoute('clear_sec');
        if($clear_sec != ''){
            $ReportSession->getManager()->getStorage()->clear('ReportSession');  
			$ReportSession->data = array('mode'=>'start_plan');
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index-old',)); 
            exit();
        }
		
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
        }else{
			if(isset($ReportSession->data)){
				$data = $ReportSession->data;
			}else{
				$data = array();    
			}			    
        }		
		if(!isset($data['port_id'])){$data['port_id'] = '';}
		if(!isset($data['index_id'])){$data['index_id'] = '';}
		if(!isset($data['date_from'])){$data['date_from'] = '';}
		if(!isset($data['date_to'])){$data['date_to'] = '';}
		if(!isset($data['future_contact'])){$data['future_contact'] = '';}
		if(!isset($data['option_contact'])){$data['option_contact'] = '';}
		if(!isset($data['range_from'])){$data['range_from'] = '900';}
		if(!isset($data['range_to'])){$data['range_to'] = '1300';}
		if(!isset($data['offset'])){$data['offset'] = '1';}
		
		$complete_message = '';
        $error_message = '';
		
		if(isset($ReportSession->complete_message) and $ReportSession->complete_message != ''){
			$complete_message = $ReportSession->complete_message;
			$ReportSession->complete_message = '';
		}
		
		if(isset($ReportSession->error_message) and $ReportSession->error_message != ''){
			$error_message = $ReportSession->error_message;
			$ReportSession->error_message = '';
		}
		
		//print_r($data); echo "<br><br>";
		$ResultPort = $PortTable->getPort('');
		$ResultIndex = $IndexTable->getIndex('');
		$ResultSymbol = $SymbolTable->getSymbol('', $data['index_id']);
		
		$ResultFuturePlan = array();
		$ResultOptionPlan = array();
		
		$mode = $this->getRequest()->getPost('mode');
		//echo 'mode => '.$mode.'<br/>'; 
		if($mode == 'add_port'){
			if($data['port_name'] != ''){
				$AddPort = array();
				$AddPort['port_name'] = $data['port_name'];
				$data['port_id'] = $PortTable->AddPort($AddPort);
				$ReportSession->complete_message = 'Add Port Complete';
				$data['mode'] == 'start_plan';
				$ReportSession->data = $data;
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
			}
		}
		
		if($mode == 'add_index'){
			if($data['index_name'] != ''){
				$AddIndex = array();
				$AddIndex['index_name'] = $data['index_name'];
				$AddIndex['future_contact'] = $data['new_future_contact'];
				$AddIndex['option_contact'] = $data['new_option_contact'];
				$data['index_id'] = $IndexTable->AddIndex($AddIndex);
				$data['future_contact'] = $data['new_future_contact'];
				$data['option_contact'] = $data['new_option_contact'];
			
				$ReportSession->complete_message = 'Add Index Complete';
				$data['mode'] == 'start_plan';
				$ReportSession->data = $data;
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
			}
		}
		
		if((isset($data['mode']) and $data['mode'] == 'start_plan') || $mode == 'change_value'){
			$future_date = array();
			$future_symbol = array();
			$future_price = array();
			$future_amount = array();
			$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			if(!empty($ResultFuturePlan)){
				foreach($ResultFuturePlan as $future_plan){
					$future_date[] = $future_plan['date'];
					$future_symbol[] = $future_plan['symbol'];
					$future_price[] = $future_plan['price'];
					$future_amount[] = $future_plan['amount'];
				}
			}
			
			$data['future_date'] = $future_date;
			$data['future_symbol'] = $future_symbol;
			$data['future_price'] = $future_price;
			$data['future_amount'] = $future_amount;
			
			$option_date = array();
			$option_symbol = array();
			$option_type = array();
			$option_price = array();
			$option_premium = array();
			$option_amount = array();
			$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			if(!empty($ResultOptionPlan)){
				foreach($ResultOptionPlan as $option_plan){
					$option_date[] = $option_plan['date'];
					$option_symbol[] = $option_plan['symbol'];
					$option_type[] = $option_plan['type'];
					$option_price[] = $option_plan['price'];
					$option_premium[] = $option_plan['premium'];
					$option_amount[] = $option_plan['amount'];
				}
			}
			
			$data['option_date'] = $option_date;
			$data['option_symbol'] = $option_symbol;
			$data['option_type'] = $option_type;
			$data['option_price'] = $option_price;
			$data['option_premium'] = $option_premium;
			$data['option_amount'] = $option_amount;
			$data['mode'] = '';
		}
		
		$ReportSession->data = $data;
		if($mode == 'save_value' || $mode == 'save_value2'){
			$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			
			if($mode == 'save_value2'){
				if(isset($data['port_name']) and $data['port_name'] != '' and $data['port_id'] == ''){
					$AddPort = array();
					$AddPort['port_name'] = $data['port_name'];
					$port_id = $PortTable->AddPort($AddPort);
				}
				
				/*$AddIndex = array();
				$AddIndex['index_name'] = $data['index_name'];
				$AddIndex['future_contact'] = $data['new_future_contact'];
				$AddIndex['option_contact'] = $data['new_option_contact'];
				$index_id = $IndexTable->AddIndex($AddIndex);*/
				$index_id = '';
				$data['future_contact'] = $data['new_future_contact'];
				$data['option_contact'] = $data['new_option_contact'];
				
			}else{
				$port_id = $data['port_id'];
				$index_id = $data['index_id'];
			}
			
			if(isset($data['future_date']) and !empty($data['future_date'])){
				foreach($data['future_date'] as $id => $future_date){
					$UpdateFuture = array();
					$UpdateFuture['date'] = $data['future_date'][$id];
					$UpdateFuture['symbol'] = $data['future_symbol'][$id];
					$UpdateFuture['price'] = $data['future_price'][$id];
					$UpdateFuture['amount'] = $data['future_amount'][$id];
					if(isset($ResultFuturePlan[$id])){
						$FuturePlanTable->UpdateFuturePlan($UpdateFuture,$ResultFuturePlan[$id]['future_id']);						
						unset($ResultFuturePlan[$id]);
					}else{
						$UpdateFuture['port_id'] = $port_id;
						$UpdateFuture['index_id'] = $index_id;
						$FuturePlanTable->AddFuturePlan($UpdateFuture);
					}
				}
			}
			
			if(isset($data['option_date']) and !empty($data['option_date'])){ 
				foreach($data['option_date'] as $id => $option_date){
					$UpdateOption = array();
					$UpdateOption['date'] = $data['option_date'][$id];
					$UpdateOption['symbol'] = $data['option_symbol'][$id];
					$UpdateOption['type'] = $data['option_type'][$id];
					$UpdateOption['price'] = $data['option_price'][$id];
					$UpdateOption['premium'] = $data['option_premium'][$id];
					$UpdateOption['amount'] = $data['option_amount'][$id];
					
					if(isset($ResultOptionPlan[$id])){
						$OptionPlanTable->UpdateOptionPlan($UpdateOption,$ResultOptionPlan[$id]['option_id']);						
						unset($ResultOptionPlan[$id]);
					}else{
						$UpdateOption['port_id'] = $port_id;
						$UpdateOption['index_id'] = $index_id;
						$OptionPlanTable->AddOptionPlan($UpdateOption);
					}
				}
			}
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
		}
		
		$SumFuture = array();
		$SumOption = array();
		$Total = array();
		$SumFutureSimulate = array();
		$SumOptionSimulate = array();
		$TotalSimulate = array();
		$TotalSum = array();
		//echo 'mode => '.$mode.'<br/>';
		if($mode == 'calculate_value'){
			//var_dump($data['range_from']);
			//var_dump($data['range_to']);
			//var_dump($data['offset']);
			if($data['range_from'] != '' and $data['range_to'] != ''){
				$time_from = strtotime($data['range_from']);
				$time_to = strtotime($data['range_to']);
				$date_from2 = $data['range_from'];
				$date_to2 = $data['range_to'];
				if($time_to > $time_from){
					$data['range_from'] = $date_from2;
					$data['range_to'] = $date_to2;
				}else{
					$data['range_to'] = $date_from2;
					$data['range_from'] = $date_to2;
				}
				if($data['range_to'] > $data['range_from'] and $data['offset'] != 0){
					$check_price = $data['range_from'];
					while($check_price <= $data['range_to']){
						if(!isset($TotalSum[$check_price])){$TotalSum[$check_price] = 0;}
						
						if(isset($data['future_date']) and !empty($data['future_date'])){
							foreach($data['future_date'] as $id => $future_date){
								$future_symbol = $data['future_symbol'][$id];
								$future_price = $data['future_price'][$id];
								$future_amount = $data['future_amount'][$id];
								$payoff = $FuturePlanTable->CalculateFuturePlan($check_price, $future_price, $future_amount);
								if(!isset($SumFuture[$check_price])){$SumFuture[$check_price] = 0;}
								if(!isset($Total[$check_price])){$Total[$check_price] = 0;}
								$SumFuture[$check_price] = $SumFuture[$check_price] + $payoff;
								$Total[$check_price] = $Total[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($data['option_date']) and !empty($data['option_date'])){ 
							foreach($data['option_date'] as $id => $option_date){ 
								$option_symbol = $data['option_symbol'][$id];
								$option_type = $data['option_type'][$id];
								$option_price = $data['option_price'][$id];
								$option_premium = $data['option_premium'][$id];
								$option_amount = $data['option_amount'][$id];
								$payoff = $OptionPlanTable->CalculateOptionPlan($check_price, $option_type, $option_price, $option_premium, $option_amount);
								if(!isset($SumOption[$check_price])){$SumOption[$check_price] = 0;}
								if(!isset($Total[$check_price])){$Total[$check_price] = 0;}
								$SumOption[$check_price] = $SumOption[$check_price] + $payoff;
								$Total[$check_price] = $Total[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($data['simulator_future_date']) and !empty($data['simulator_future_date'])){
							foreach($data['simulator_future_date'] as $id => $future_date){
								$future_symbol = $data['simulator_future_symbol'][$id];
								$future_price = $data['simulator_future_price'][$id];
								$future_amount = $data['simulator_future_amount'][$id];
								$payoff = $FuturePlanTable->CalculateFuturePlan($check_price, $future_price, $future_amount);
								if(!isset($SumFutureSimulate[$check_price])){$SumFutureSimulate[$check_price] = 0;}
								if(!isset($TotalSimulate[$check_price])){$TotalSimulate[$check_price] = 0;}
								$SumFutureSimulate[$check_price] = $SumFutureSimulate[$check_price] + $payoff;
								$TotalSimulate[$check_price] = $TotalSimulate[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($data['simulator_option_date']) and !empty($data['simulator_ption_date'])){ 
							foreach($data['simulator_option_date'] as $id => $option_date){ 
								$option_symbol = $data['simulator_option_symbol'][$id];
								$option_type = $data['simulator_option_type'][$id];
								$option_price = $data['simulator_option_price'][$id];
								$option_premium = $data['simulator_option_premium'][$id];
								$option_amount = $data['simulator_option_amount'][$id];
								$payoff = $OptionPlanTable->CalculateOptionPlan($check_price, $option_type, $option_price, $option_premium, $option_amount);
								if(!isset($SumOptionSimulate[$check_price])){$SumOptionSimulate[$check_price] = 0;}
								if(!isset($TotalSimulate[$check_price])){$TotalSimulate[$check_price] = 0;}
								$SumOptionSimulate[$check_price] = $SumOptionSimulate[$check_price] + $payoff;
								$TotalSimulate[$check_price] = $TotalSimulate[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						//echo 'SumFuture => '; var_dump($SumFuture); echo '<br/><br/>';
						//echo 'SumOption => '; var_dump($SumOption); echo '<br/><br/>';
						$check_price = $check_price + $data['offset'];
						
					}
				}
			}
		}
		
		$page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        //echo $this->params()->fromRoute('per_page');
        if($this->getRequest()->isPost('per_page')){
            $per_page = $this->getRequest()->getPost('per_page');
        }elseif(isset($ReportSession->per_page)){
            $per_page = $ReportSession->per_page;
        }else{
            $per_page = 10;
        }		
		if(!isset($per_page)){$per_page = 10;}
       
		$ReportSession->per_page = $per_page;
        $paginator = new Paginator(new ArrayAdapter($ResultPort));
        $paginator->setCurrentPageNumber($page)
        ->setItemCountPerPage($per_page)
        ;
		
        return new ViewModel(array(            
            'baseUrl' => $this->getBaseUrl(),
            'data' => $data,
            'ResultPort' => $ResultPort,
            'ResultIndex' => $ResultIndex,
            'ResultFuturePlan' => $ResultFuturePlan,
            'complete_message' => $complete_message,
            'error_message' => $error_message,
            'paginator' => $paginator,
            'page' => $page,
            'per_page' => $per_page,
			'lang' => $this->Language,
			'SumFuture' => $SumFuture,
			'SumOption' => $SumOption,
			'SumFutureSimulate' => $SumFutureSimulate,
			'SumOptionSimulate' => $SumOptionSimulate,
			'Total' => $Total,
			'TotalSimulate' => $TotalSimulate,
			'TotalSum' => $TotalSum,
        )); 
		
	}
	
	public function indexOld2Action()
    {
		$ReportSession = new Container('ReportSession');
		$PortTable = $this->getServiceLocator()->get('PortTable');
		$IndexTable = $this->getServiceLocator()->get('IndexTable');
		$SymbolTable = $this->getServiceLocator()->get('SymbolTable');
		$FuturePlanTable = $this->getServiceLocator()->get('FuturePlanTable');
		$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		//$FuturePlanTable = new Container('FuturePlanTable');
		//$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		$clear_sec = $this->params()->fromRoute('clear_sec');
        if($clear_sec != ''){
            $ReportSession->getManager()->getStorage()->clear('ReportSession');  
			$ReportSession->data = array('mode'=>'start_plan');
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
            exit();
        }
		
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
        }else{
			if(isset($ReportSession->data)){
				$data = $ReportSession->data;
			}else{
				$data = array();    
			}			    
        }		
		if(!isset($data['port_id'])){$data['port_id'] = '';}
		if(!isset($data['port_id2'])){$data['port_id2'] = '';}
		if(!isset($data['index_id'])){$data['index_id'] = '';}
		if(!isset($data['date_from'])){$data['date_from'] = '';}
		if(!isset($data['date_to'])){$data['date_to'] = '';}
		if(!isset($data['future_contact'])){$data['future_contact'] = '';}
		if(!isset($data['option_contact'])){$data['option_contact'] = '';}
		if(!isset($data['current_price'])){$data['current_price'] = '1000';}
		if(!isset($data['range_from'])){$data['range_from'] = '900';}
		if(!isset($data['range_to'])){$data['range_to'] = '1300';}
		if(!isset($data['offset'])){$data['offset'] = '1';}
		
		$complete_message = '';
        $error_message = '';
		
		
		if(isset($ReportSession->complete_message) and $ReportSession->complete_message != ''){
			$complete_message = $ReportSession->complete_message;
			$ReportSession->complete_message = '';
		}
		
		if(isset($ReportSession->error_message) and $ReportSession->error_message != ''){
			$error_message = $ReportSession->error_message;
			$ReportSession->error_message = '';
		}
		
		//print_r($data); echo "<br><br>";
		$ResultPort = $PortTable->getPort('');
		$ResultIndex = $IndexTable->getIndex('');
		$ResultSymbol = $SymbolTable->getSymbol('', $data['index_id']);
		
		if(isset($ReportSession->ResultFuturePlan)){
			$ResultFuturePlan = $ReportSession->ResultFuturePlan;
		}else{
			$ResultFuturePlan = array();
		}
		
		if(isset($ReportSession->ResultOptionPlan)){
			$ResultOptionPlan = $ReportSession->ResultOptionPlan;
		}else{
			$ResultOptionPlan = array();
		}
		
		if(isset($ReportSession->ResultFuturePlan2)){
			$ResultFuturePlan2 = $ReportSession->ResultFuturePlan2;
		}else{
			$ResultFuturePlan2 = array();
		}
		
		if(isset($ReportSession->ResultOptionPlan2)){
			$ResultOptionPlan2 = $ReportSession->ResultOptionPlan2;
		}else{
			$ResultOptionPlan2 = array();
		}
		
		$mode = $this->getRequest()->getPost('mode');
		//echo 'mode => '.$mode.'<br/>'; 
		if($mode == 'add_port'){
			if($data['port_name'] != ''){
				$AddPort = array();
				$AddPort['port_name'] = $data['port_name'];
				$data['port_id'] = $PortTable->AddPort($AddPort);
				$ReportSession->complete_message = 'Add Port Complete';
				$data['mode'] == 'start_plan';
				$ReportSession->data = $data;
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
			}
		}
		
		if($mode == 'add_index'){
			if($data['index_name'] != ''){
				$AddIndex = array();
				$AddIndex['index_name'] = $data['index_name'];
				$AddIndex['future_contact'] = $data['new_future_contact'];
				$AddIndex['option_contact'] = $data['new_option_contact'];
				$data['index_id'] = $IndexTable->AddIndex($AddIndex);
				$data['future_contact'] = $data['new_future_contact'];
				$data['option_contact'] = $data['new_option_contact'];
			
				$ReportSession->complete_message = 'Add Index Complete';
				$data['mode'] == 'start_plan';
				$ReportSession->data = $data;
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
			}
		}
		
		if((isset($data['mode']) and $data['mode'] == 'start_plan') || $mode == 'change_value'){
			
			if(isset($data['port_id']) and $data['port_id'] != ''){
				$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			}else{
				$ResultFuturePlan = array();
				$ResultFuturePlan = array();
			}
			
			if(isset($data['port_id2']) and $data['port_id2'] != ''){
				$ResultFuturePlan2 = $FuturePlanTable->getFuturePlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan2 = $OptionPlanTable->getOptionPlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
			}else{
				$ResultFuturePlan2 = array();
				$ResultOptionPlan2 = array();
			}			

			$data['mode'] = '';
		}
		
		if($mode == 'change_port'){
			if(isset($data['port_id']) and $data['port_id'] != ''){
				$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			}else{
				$ResultFuturePlan = array();
				$ResultFuturePlan = array();
			}
		}
		
		if($mode == 'change_port2'){
			if(isset($data['port_id2']) and $data['port_id2'] != ''){
				$ResultFuturePlan2 = $FuturePlanTable->getFuturePlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan2 = $OptionPlanTable->getOptionPlan('', $data['port_id2'], $data['index_id'], $data['date_from'], $data['date_to']);
			}else{
				$ResultFuturePlan2 = array();
				$ResultOptionPlan2 = array();
			}
		}
		
		if($mode == 'add_future_plan'){
			if(isset($data['future_date']) and !empty($data['future_date'])){
				foreach($data['future_date'] as $id => $future_date){
					$UpdateFuture = array();
					$UpdateFuture['port_id'] = $data['port_id'];
					$UpdateFuture['date'] = $data['future_date'][$id];
					$UpdateFuture['symbol'] = $data['future_symbol'][$id];
					$UpdateFuture['price'] = $data['future_price'][$id];
					$UpdateFuture['amount'] = $data['future_amount'][$id];
					$ResultFuturePlan[] = $UpdateFuture;
				}
			}
		}
		
		if($mode == 'add_future_plan_2'){
			if(isset($data['future_date']) and !empty($data['future_date'])){
				foreach($data['future_date'] as $id => $future_date){
					$UpdateFuture = array();
					$UpdateFuture['port_id'] = $data['port_id'];
					$UpdateFuture['date'] = $data['future_date'][$id];
					$UpdateFuture['symbol'] = $data['future_symbol'][$id];
					$UpdateFuture['price'] = $data['future_price'][$id];
					$UpdateFuture['amount'] = $data['future_amount'][$id];
					$ResultFuturePlan2[] = $UpdateFuture;
				}
			}
		}
		
		$ReportSession->ResultFuturePlan = $ResultFuturePlan;
		$ReportSession->ResultOptionPlan = $ResultOptionPlan;
		
		$ReportSession->ResultFuturePlan2 = $ResultFuturePlan2;
		$ReportSession->ResultOptionPlan2 = $ResultOptionPlan2;
		
		$ReportSession->data = $data;
		if($mode == 'save_value' || $mode == 'save_value2'){
			$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			
			if($mode == 'save_value2'){
				if(isset($data['port_name']) and $data['port_name'] != '' and $data['port_id'] == ''){
					$AddPort = array();
					$AddPort['port_name'] = $data['port_name'];
					$port_id = $PortTable->AddPort($AddPort);
				}
				
				/*$AddIndex = array();
				$AddIndex['index_name'] = $data['index_name'];
				$AddIndex['future_contact'] = $data['new_future_contact'];
				$AddIndex['option_contact'] = $data['new_option_contact'];
				$index_id = $IndexTable->AddIndex($AddIndex);*/
				$index_id = '';
				$data['future_contact'] = $data['new_future_contact'];
				$data['option_contact'] = $data['new_option_contact'];
				
			}else{
				$port_id = $data['port_id'];
				$index_id = $data['index_id'];
			}
			
			if(isset($data['future_date']) and !empty($data['future_date'])){
				foreach($data['future_date'] as $id => $future_date){
					$UpdateFuture = array();
					$UpdateFuture['date'] = $data['future_date'][$id];
					$UpdateFuture['symbol'] = $data['future_symbol'][$id];
					$UpdateFuture['price'] = $data['future_price'][$id];
					$UpdateFuture['amount'] = $data['future_amount'][$id];
					if(isset($ResultFuturePlan[$id])){
						$FuturePlanTable->UpdateFuturePlan($UpdateFuture,$ResultFuturePlan[$id]['future_id']);						
						unset($ResultFuturePlan[$id]);
					}else{
						$UpdateFuture['port_id'] = $port_id;
						$UpdateFuture['index_id'] = $index_id;
						$FuturePlanTable->AddFuturePlan($UpdateFuture);
					}
				}
			}
			
			if(isset($data['option_date']) and !empty($data['option_date'])){ 
				foreach($data['option_date'] as $id => $option_date){
					$UpdateOption = array();
					$UpdateOption['date'] = $data['option_date'][$id];
					$UpdateOption['symbol'] = $data['option_symbol'][$id];
					$UpdateOption['type'] = $data['option_type'][$id];
					$UpdateOption['price'] = $data['option_price'][$id];
					$UpdateOption['premium'] = $data['option_premium'][$id];
					$UpdateOption['amount'] = $data['option_amount'][$id];
					
					if(isset($ResultOptionPlan[$id])){
						$OptionPlanTable->UpdateOptionPlan($UpdateOption,$ResultOptionPlan[$id]['option_id']);						
						unset($ResultOptionPlan[$id]);
					}else{
						$UpdateOption['port_id'] = $port_id;
						$UpdateOption['index_id'] = $index_id;
						$OptionPlanTable->AddOptionPlan($UpdateOption);
					}
				}
			}
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'index',)); 
		}		
		
		$SumFuture = array();
		$SumOption = array();
		$Total = array();
		
		$SumFuture2 = array();
		$SumOption2 = array();
		$Total2 = array();
		
		$TotalSum = array();
		$check_current = 0;
		$current_future_payoff = 0;
		$current_option_payoff = 0;
		
		$current_port1_payoff = 0;
		$current_port2_payoff = 0;
		
		//echo 'mode => '.$mode.'<br/>';
		//if($mode == 'calculate_value'){
			//var_dump($data['range_from']);
			//var_dump($data['range_to']);
			//var_dump($data['offset']);
			if($data['range_from'] != '' and $data['range_to'] != ''){
				$time_from = strtotime($data['range_from']);
				$time_to = strtotime($data['range_to']);
				$date_from2 = $data['range_from'];
				$date_to2 = $data['range_to'];
				if($time_to > $time_from){
					$data['range_from'] = $date_from2;
					$data['range_to'] = $date_to2;
				}else{
					$data['range_to'] = $date_from2;
					$data['range_from'] = $date_to2;
				}
				if($data['range_to'] > $data['range_from'] and $data['offset'] != 0){
					$check_price = $data['range_from'];
					while($check_price <= $data['range_to']){
						if(!isset($TotalSum[$check_price])){$TotalSum[$check_price] = 0;}
						
						if(isset($ResultFuturePlan) and !empty($ResultFuturePlan)){
							foreach($ResultFuturePlan as $id => $reault_future_plan){
								$future_date = $reault_future_plan['date'];
								$future_symbol = $reault_future_plan['symbol'];
								$future_price = $reault_future_plan['price'];
								$future_amount = $reault_future_plan['amount'];
								
								$payoff = $FuturePlanTable->CalculateFuturePlan($check_price, $future_price, $future_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_future_payoff = $current_future_payoff + $payoff;
									$current_port1_payoff = $current_port1_payoff + $payoff;
									$check_current++;
								}
								
								if(!isset($SumFuture[$check_price])){$SumFuture[$check_price] = 0;}
								if(!isset($Total[$check_price])){$Total[$check_price] = 0;}
								$SumFuture[$check_price] = $SumFuture[$check_price] + $payoff;
								$Total[$check_price] = $Total[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($ResultOptionPlan) and !empty($ResultOptionPlan)){ 
							foreach($ResultOptionPlan as $id => $result_option){ 
								$option_date = $result_option['date'];
								$option_symbol = $result_option['symbol'];
								$option_type = $result_option['type'];
								$option_price = $result_option['price'];
								$option_premium = $result_option['premium'];
								$option_amount = $result_option['amount'];
								$payoff = $OptionPlanTable->CalculateOptionPlan($check_price, $option_type, $option_price, $option_premium, $option_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_option_payoff = $current_option_payoff + $payoff;
									$current_port1_payoff = $current_port1_payoff + $payoff;
									$check_current++;
								}
								
								if(!isset($SumOption[$check_price])){$SumOption[$check_price] = 0;}
								if(!isset($Total[$check_price])){$Total[$check_price] = 0;}
								$SumOption[$check_price] = $SumOption[$check_price] + $payoff;
								$Total[$check_price] = $Total[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($ResultFuturePlan2) and !empty($ResultFuturePlan2)){
							foreach($ResultFuturePlan2 as $id => $reault_future_plan){
								$future_date = $reault_future_plan['date'];
								$future_symbol = $reault_future_plan['symbol'];
								$future_price = $reault_future_plan['price'];
								$future_amount = $reault_future_plan['amount'];
								
								$payoff = $FuturePlanTable->CalculateFuturePlan($check_price, $future_price, $future_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_future_payoff = $current_future_payoff + $payoff;
									$current_port2_payoff = $current_port2_payoff + $payoff;
									$check_current++;
								}
								if(!isset($SumFuture2[$check_price])){$SumFuture2[$check_price] = 0;}
								if(!isset($Total2[$check_price])){$Total2[$check_price] = 0;}
								$SumFuture2[$check_price] = $SumFuture2[$check_price] + $payoff;
								$Total2[$check_price] = $Total2[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						if(isset($ResultOptionPlan2) and !empty($ResultOptionPlan2)){ 
							foreach($ResultOptionPlan2 as $id => $result_option){ 
								$option_date = $result_option['date'];
								$option_symbol = $result_option['symbol'];
								$option_type = $result_option['type'];
								$option_price = $result_option['price'];
								$option_premium = $result_option['premium'];
								$option_amount = $result_option['amount'];
								$payoff = $OptionPlanTable->CalculateOptionPlan($check_price, $option_type, $option_price, $option_premium, $option_amount);
								if(isset($data['current_price']) and $data['current_price'] == $check_price){
									$current_option_payoff = $current_option_payoff + $payoff;
									$current_port2_payoff = $current_port2_payoff + $payoff;
									$check_current++;
								}
								if(!isset($SumOption2[$check_price])){$SumOption2[$check_price] = 0;}
								if(!isset($Total2[$check_price])){$Total2[$check_price] = 0;}
								$SumOption2[$check_price] = $SumOption2[$check_price] + $payoff;
								$Total2[$check_price] = $Total2[$check_price] + $payoff;
								$TotalSum[$check_price] = $TotalSum[$check_price] + $payoff;
							}
						}
						
						//echo 'SumFuture => '; var_dump($SumFuture); echo '<br/><br/>';
						//echo 'SumOption => '; var_dump($SumOption); echo '<br/><br/>';
						$check_price = $check_price + $data['offset'];
						
					}
				}
			}
		//}
		
		if($data['current_price'] != '' and $check_current == 0){
			if(isset($ResultFuturePlan) and !empty($ResultFuturePlan)){
				foreach($ResultFuturePlan as $id => $reault_future_plan){
					$payoff = $FuturePlanTable->CalculateFuturePlan($data['current_price'], $future_price, $future_amount);
					$current_future_payoff = $current_future_payoff + $payoff;
				}
			}
						
			if(isset($ResultOptionPlan) and !empty($ResultOptionPlan)){ 
				foreach($ResultOptionPlan as $id => $result_option){ 
					$payoff = $OptionPlanTable->CalculateOptionPlan($data['current_price'], $option_type, $option_price, $option_premium, $option_amount);
					$current_option_payoff = $current_option_payoff + $payoff;
				}
			}
			
			if(isset($ResultFuturePlan2) and !empty($ResultFuturePlan2)){
				foreach($ResultFuturePlan2 as $id => $reault_future_plan){
					$payoff = $FuturePlanTable->CalculateFuturePlan($data['current_price'], $future_price, $future_amount);
					$current_future_payoff = $current_future_payoff + $payoff;
				}
			}
						
			if(isset($ResultOptionPlan2) and !empty($ResultOptionPlan2)){ 
				foreach($ResultOptionPlan2 as $id => $result_option){ 
					$payoff = $OptionPlanTable->CalculateOptionPlan($data['current_price'], $option_type, $option_price, $option_premium, $option_amount);
					$current_option_payoff = $current_option_payoff + $payoff;
				}
			}
		}
		
		if(!isset($data['page1'])){$data['page1'] = 1;}
		if(!isset($data['per_page1'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page1'] = $ReportSession->per_page1;
			}else{
				$data['per_page1'] = 20;
			}
		}
		if($data['per_page1'] == ''){$data['per_page1'] = 20;}
		
		if(!isset($data['page2'])){$data['page2'] = 1;}
		if(!isset($data['per_page2'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page2'] = $ReportSession->per_page1;
			}else{
				$data['per_page2'] = 20;
			}
		}
		if($data['per_page2'] == ''){$data['per_page2'] = 20;}
		
		if(!isset($data['page3'])){$data['page3'] = 1;}
		if(!isset($data['per_page3'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page3'] = $ReportSession->per_page1;
			}else{
				$data['per_page3'] = 20;
			}
		}
		if($data['per_page3'] == ''){$data['per_page3'] = 20;}
		
		if(!isset($data['page4'])){$data['page4'] = 1;}
		if(!isset($data['per_page4'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page4'] = $ReportSession->per_page1;
			}else{
				$data['per_page4'] = 20;
			}
		}
		if($data['per_page4'] == ''){$data['per_page4'] = 20;}
		
		if($mode == 'Changepage'){
			$page_name = $data['change_page_name'];
			$page_value = $data['change_page_value'];
			$data[$page_name] = $page_value;
		}
	   
		$ReportSession->per_page1 = $data['per_page1'];
        $paginator_future1 = new Paginator(new ArrayAdapter($ResultFuturePlan));
        $paginator_future1->setCurrentPageNumber($data['page1'])
        ->setItemCountPerPage($data['per_page1'])
        ;
		$future_page_slide = $this->GeneratePaginationSlide($paginator_future1, $data['page1'], $data['per_page1'], 'page1');
		
		$ReportSession->per_page2 = $data['per_page2'];
        $paginator_future2 = new Paginator(new ArrayAdapter($ResultFuturePlan2));
        $paginator_future2->setCurrentPageNumber($data['page2'])
        ->setItemCountPerPage($data['per_page2'])
        ;
		$future_page_slide2 = $this->GeneratePaginationSlide($paginator_future2, $data['page2'], $data['per_page2'], 'page2');
		
		$ReportSession->per_page3 = $data['per_page3'];
        $paginator_option1 = new Paginator(new ArrayAdapter($ResultOptionPlan));
        $paginator_option1->setCurrentPageNumber($data['page3'])
        ->setItemCountPerPage($data['per_page3'])
        ;
		$option_page_slide = $this->GeneratePaginationSlide($paginator_option1, $data['page3'], $data['per_page3'], 'page3');
		
		$ReportSession->per_page4 = $data['per_page4'];
        $paginator_option2 = new Paginator(new ArrayAdapter($ResultOptionPlan2));
        $paginator_option2->setCurrentPageNumber($data['page4'])
        ->setItemCountPerPage($data['per_page4'])
        ;
		$option_page_slide2 = $this->GeneratePaginationSlide($paginator_option2, $data['page4'], $data['per_page4'], 'page4');
		
        return new ViewModel(array(            
            'baseUrl' => $this->getBaseUrl(),
            'data' => $data,
            'ResultPort' => $ResultPort,
            'ResultIndex' => $ResultIndex,
            'ResultFuturePlan' => $ResultFuturePlan,
            'ResultFuturePlan2' => $ResultFuturePlan2,
            'ResultOptionPlan' => $ResultOptionPlan,
            'ResultOptionPlan2' => $ResultOptionPlan2,
            'complete_message' => $complete_message,
            'error_message' => $error_message,
            'paginator_future1' => $paginator_future1,
            'paginator_future2' => $paginator_future2,
            'paginator_option1' => $paginator_option1,
            'paginator_option2' => $paginator_option2,
			'lang' => $this->Language,
			'SumFuture' => $SumFuture,
			'SumOption' => $SumOption,
			'SumFuture2' => $SumFuture2,
			'SumOption2' => $SumOption2,
			'Total' => $Total,
			'Total2' => $Total2,
			'TotalSum' => $TotalSum,
			'future_page_slide' => $future_page_slide,
			'future_page_slide2' => $future_page_slide2,
			'option_page_slide' => $option_page_slide,
			'option_page_slide2' => $option_page_slide2,
			'current_future_payoff' => $current_future_payoff,
			'current_option_payoff' => $current_option_payoff,
			'current_port1_payoff' => $current_port1_payoff,
			'current_port2_payoff' => $current_port2_payoff,
        )); 
		
	}
	
	private function GeneratePaginationSlide($paginator, $page, $per_page, $page_name)
	{
		$page_data = $paginator->getPages();
		$page_slide = '<div class="pagination"> ';
		//<!--First page link -->        
		if (isset($page_data->previous)):            
			$page_slide.='<a href="#" onClick="javascript:Changepage(\''.$page_name.'\', 1);"><i class="icon-first"></i></a>';
		else:              
			$page_slide.='<span class="disabled"><i class="icon-first"></i></span>';
		endif;         
			
		if($page_data->current-10 > 0){
			$page_slide.='<a href="#"  onClick="javascript:Changepage(\''.$page_name.'\', '.($page_data->current-10).');">'.($page_data->current-10).'</a> ...';
		}
		
		//<!-- Previous page link -->
		if (isset($page_data->previous)){
			$page_slide.='<a href="#" onClick="javascript:Changepage(\''.$page_name.'\', '.$page_data->previous.');" ><i class="icon-previous2"></i></a>';
		}else{
			$page_slide.='<span class="disabled"><i class="icon-previous2"></i></span>';
		}
		
		//<!-- Numbered page links -->
		for($page1=($page_data->current-3); $page1<($page_data->current+3); $page1++){
			if($page1>0 and $page1 <= $page_data->last){
				if ($page1 != $page_data->current){
					$page_slide.='<a href="#" onClick="javascript:Changepage(\''.$page_name.'\', '.$page1.');" >'.$page1.'</a>';
				}else{$page_slide.= $page1;}				
			}
		}
		
		//<!-- Next page link -->
		if (isset($page_data->next)):
			$page_slide.='<a href="#" onClick="javascript:Changepage(\''.$page_name.'\', '.$page_data->next.');"><i class="icon-next2"></i></a>';
		else:
			$page_slide.='<span class="disabled"><i class="icon-next2"></i></span>';
		endif;    
			
		if($page_data->current+10 <= $page_data->last){
			$page_slide.='... <a href="#" onClick="javascript:Changepage(\''.$page_name.'\', '.($page_data->current+10).');">'.($page_data->current+10).'</a>';
		}   
			
		//<!-- Last page link -->        
		if (isset($page_data->next)):
			$page_slide.='<a href="#"  onClick="javascript:Changepage(\''.$page_name.'\', '.($page_data->last).');"><i class="icon-last"></i></a>';
		else:
			$page_slide.='<span class="disabled"><i class="icon-last"></i></span>';
		endif;
		
		$page_slide.='&nbsp;From&nbsp;'.$page_data->last.'&nbsp;Page';
			
		$page_slide.='</div>';
		
		
		
		return $page_slide;
	}
	
	public function stockSettingAction()
    {
		$ReportSession = new Container('ReportSession');
		$PortTable = $this->getServiceLocator()->get('PortTable');
		$IndexTable = $this->getServiceLocator()->get('IndexTable');
		$SymbolTable = $this->getServiceLocator()->get('SymbolTable');
		$FuturePlanTable = $this->getServiceLocator()->get('FuturePlanTable');
		$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		//$FuturePlanTable = new Container('FuturePlanTable');
		//$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		$clear_sec = $this->params()->fromRoute('clear_sec');
        if($clear_sec != ''){
            $ReportSession->getManager()->getStorage()->clear('ReportSession');  
			$ReportSession->data = array('mode'=>'start_plan');
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
            exit();
        }
		
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
        }else{
			if(isset($ReportSession->data)){
				$data = $ReportSession->data;
			}else{
				$data = array();    
			}			    
        }		
		if(!isset($data['show_tabs'])){$data['show_tabs'] = '#future_plan';}
		if(!isset($data['port_id'])){$data['port_id'] = '';}
		if(!isset($data['port_id2'])){$data['port_id2'] = '';}
		if(!isset($data['index_id'])){$data['index_id'] = '';}
		if(!isset($data['type'])){$data['type'] = '';}
		if(!isset($data['date_from'])){$data['date_from'] = '';}
		if(!isset($data['date_to'])){$data['date_to'] = '';}
		if(!isset($data['future_contact'])){$data['future_contact'] = '';}
		if(!isset($data['option_contact'])){$data['option_contact'] = '';}
		if(!isset($data['range_from'])){$data['range_from'] = '900';}
		if(!isset($data['range_to'])){$data['range_to'] = '1300';}
		if(!isset($data['offset'])){$data['offset'] = '1';}
		
		$complete_message = '';
        $error_message = '';
		
		if(isset($ReportSession->complete_message) and $ReportSession->complete_message != ''){
			$complete_message = $ReportSession->complete_message;
			$ReportSession->complete_message = '';
		}
		
		if(isset($ReportSession->error_message) and $ReportSession->error_message != ''){
			$error_message = $ReportSession->error_message;
			$ReportSession->error_message = '';
		}
		
		//print_r($data); echo "<br><br>";
		$ResultPort = $PortTable->getPort('');
		$ResultIndex = $IndexTable->getIndex('');
		$ResultSymbol = $SymbolTable->getSymbol('', $data['index_id']);
		
		if(isset($ReportSession->ResultImport)){
			$ResultImport = $ReportSession->ResultImport;
			$ReportSession->ResultImport = array();
		}else{
			$ResultImport = array();
		}
		
		$mode = $this->getRequest()->getPost('mode');
		//echo 'mode => '.$mode.'<br/>'; 
		if($mode == 'add_port'){
			if($data['port_name'] != ''){
				$AddPort = array();
				$AddPort['port_name'] = $data['port_name'];
				$data['port_id'] = $PortTable->AddPort($AddPort);
				$ReportSession->complete_message = 'Add Port Complete';
				$data['mode'] == 'start_plan';
				$ReportSession->data = $data;
				return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
			}
		}
		
		if($mode == 'import_future_plan'){
			$post_file = $request->getFiles()->toArray();
			$temp_name = $post_file["file_master_future"]["tmp_name"];
			$file_name = $post_file["file_master_future"]["name"];
			$file_type = explode("/" , strtolower($post_file["file_master_future"]["type"]) );
			$file_size = $post_file["file_master_future"]["size"];
			
			$field_search = array();
			$product_list = array();
			if($file_size > 0){
				$file_path = APPLICATION_PATH.'/public/file/uploaded/';
				$tempname = $file_path.$temp_name;
				$filename = $file_path.$file_name;
				//echo "$temp_name, $filename <br/>";
				if(move_uploaded_file($temp_name, $filename)){					
					set_time_limit(0);			
					$objPHPExcel = new \PHPExcel();
					$objPHPExcel = \PHPExcel_IOFactory::load($filename);
			
					$objPHPExcel->setActiveSheetIndex(0);
					$sheet = $objPHPExcel->getActiveSheet();
					$flag = 0;
					
					foreach($sheet->getRowIterator() as $row){ //loop rows
						if($flag == 0){
							//echo "header excel <br/>";
							foreach($row->getCellIterator() as $key => $cell) //loop colum
							{
								$show_header[$key] = trim($cell->getCalculatedValue());                            
								$header_name = strtolower(trim($cell->getCalculatedValue())); 
								$column_name[$key] = $header_name;
							}
						}else{
							//echo "data excel<br/>";
							foreach($row->getCellIterator() as $key => $cell) //loop colum
							{
								$details[($flag-1)][$column_name[$key]] = trim($cell->getCalculatedValue());
							}
						}
						$flag++;
						//echo "<br/>";
					}
					
					$complete_import = 0;
					$error_import = 0;
					foreach($details as $detail_id => $detailss){
						$detailss['error'] = 0;
						$detailss['result'] = '';
						
						if(!isset($detailss['transaction']) || $detailss['transaction'] == ''){
							$detailss['transaction'] = '';
						}else{
							$ResultDup = $FuturePlanTable->CheckDuplicate('', $data['import_future_port_id'], $detailss['transaction']);
							if($ResultDup){
								$detailss['error']++;
								$detailss['result'] .= 'Duplicate Transaction ID, ';
							}
						}
						
						if(!isset($data['import_future_port_id']) || $data['import_future_port_id'] == ''){
						}else{
							$ResultCheckPort = $PortTable->getPort($data['import_future_port_id']);
							if(empty($ResultCheckPort)){
								$detailss['error']++;
								$detailss['result'] .= 'Incorrect Port, ';
							}
						}
						
						if(!isset($detailss['price']) || $detailss['price'] == ''){
							$detailss['error']++;
							$detailss['result'] .= 'Please Input Price, ';
						}
						if(!isset($detailss['amount']) || $detailss['amount'] == ''){
							$detailss['error']++;
							$detailss['result'] .= 'Please Input Amount, ';
						}
						
						if($detailss['error'] == 0){
							$UpdateFuture = array();
							$UpdateFuture['date'] =  date('Y-m-d',strtotime($detailss['date']));
							$UpdateFuture['transaction_id'] = $detailss['transaction'];
							$UpdateFuture['symbol'] = $detailss['symbol'];
							$UpdateFuture['price'] = $detailss['price'];
							$UpdateFuture['amount'] = $detailss['amount'];
							$UpdateFuture['port_id'] = $data['import_future_port_id'];							
							$FuturePlanTable->AddFuturePlan($UpdateFuture);
							
							$detailss['result'] .= 'Add Completed, ';
							$complete_import++;
						}else{
							$error_import++;
						}
						
						$ResultImport[] = $detailss;
					}
					
					if($complete_import > 0){
						$complete_message = 'Import Future Complete '.$complete_import.' Transaction';
						if($error_import > 0){
							$complete_message .= ', Error '.$error_import.' Transaction';
						}
					}else{
						$error_message = 'Import Future Error All '.$error_import.' Transaction';
					}
				}else{$error_message .= 'Cannot Upload File. Please Contact Administrator';}
			}else{
				$error_message .= 'No Data in File';
			}
			
			$ReportSession->ResultImport = $ResultImport;
			$ReportSession->complete_message = $complete_message;
			$ReportSession->error_message = $error_message;
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
		}
		
		if($mode == 'import_option_plan'){
			$post_file = $request->getFiles()->toArray();
			$temp_name = $post_file["file_master_option"]["tmp_name"];
			$file_name = $post_file["file_master_option"]["name"];
			$file_type = explode("/" , strtolower($post_file["file_master_option"]["type"]) );
			$file_size = $post_file["file_master_option"]["size"];
			
			$field_search = array();
			$product_list = array();
			if($file_size > 0){
				$file_path = APPLICATION_PATH.'/public/file/uploaded/';
				$tempname = $file_path.$temp_name;
				$filename = $file_path.$file_name;
				//echo "$temp_name, $filename <br/>";
				if(move_uploaded_file($temp_name, $filename)){					
					set_time_limit(0);			
					$objPHPExcel = new \PHPExcel();
					$objPHPExcel = \PHPExcel_IOFactory::load($filename);
			
					$objPHPExcel->setActiveSheetIndex(0);
					$sheet = $objPHPExcel->getActiveSheet();
					$flag = 0;
					
					foreach($sheet->getRowIterator() as $row){ //loop rows
						if($flag == 0){
							//echo "header excel <br/>";
							foreach($row->getCellIterator() as $key => $cell) //loop colum
							{
								$show_header[$key] = trim($cell->getCalculatedValue());                            
								$header_name = strtolower(trim($cell->getCalculatedValue())); 
								$column_name[$key] = $header_name;
							}
						}else{
							//echo "data excel<br/>";
							foreach($row->getCellIterator() as $key => $cell) //loop colum
							{
								$details[($flag-1)][$column_name[$key]] = trim($cell->getCalculatedValue());
							}
						}
						$flag++;
						//echo "<br/>";
					}
					
					$complete_import = 0;
					$error_import = 0;
					foreach($details as $detail_id => $detailss){
						$detailss['error'] = 0;
						$detailss['result'] = '';
						
						if(!isset($detailss['transaction']) || $detailss['transaction'] == ''){
							$detailss['transaction'] = '';
						}else{
							$ResultDup = $OptionPlanTable->CheckDuplicate('', $data['import_future_port_id'], $detailss['transaction']);
							if($ResultDup){
								$detailss['error']++;
								$detailss['result'] .= 'Duplicate Transaction ID, ';
							}
						}
						
						if(!isset($data['import_future_port_id']) || $data['import_future_port_id'] == ''){
						}else{
							$ResultCheckPort = $PortTable->getPort($data['import_future_port_id']);
							if(empty($ResultCheckPort)){
								$detailss['error']++;
								$detailss['result'] .= 'Incorrect Port, ';
							}
						}
						
						if(!isset($detailss['type']) || $detailss['type'] == ''){
							$detailss['error']++;
							$detailss['result'] .= 'Please Select Type, ';
						}else{
							if(strtolower($detailss['type']) == 'put'){
								$detailss['type'] = 'Put';
							}elseif(strtolower($detailss['type']) == 'call'){
								$detailss['type'] = 'Call';
							}else{
								$detailss['error']++;
								$detailss['result'] .= 'Incorrect Type, ';
							}
						}
						
						if(!isset($detailss['price']) || $detailss['price'] == ''){
							$detailss['error']++;
							$detailss['result'] .= 'Please Input Price, ';
						}
						if(!isset($detailss['premium']) || $detailss['premium'] == ''){
							$detailss['error']++;
							$detailss['result'] .= 'Please Input Premium, ';
						}
						if(!isset($detailss['amount']) || $detailss['amount'] == ''){
							$detailss['error']++;
							$detailss['result'] .= 'Please Input Amount, ';
						}
						
						if($detailss['error'] == 0){
							$UpdateOption = array();
							$UpdateOption['date'] = date('Y-m-d',strtotime($detailss['date']));
							$UpdateOption['transaction_id'] = $detailss['transaction'];
							$UpdateOption['symbol'] = $detailss['symbol'];
							$UpdateOption['type'] = $detailss['type'];
							$UpdateOption['price'] = $detailss['price'];
							$UpdateOption['premium'] = $detailss['premium'];
							$UpdateOption['amount'] = $detailss['amount'];
							$UpdateOption['port_id'] = $data['import_option_port_id'];							
							$OptionPlanTable->AddOptionPlan($UpdateOption);
							
							$detailss['result'] .= 'Add Completed, ';
							$complete_import++;
						}else{
							$error_import++;
						}
						
						$ResultImport[] = $detailss;
					}
					
					if($complete_import > 0){
						$complete_message = 'Import Future Complete '.$complete_import.' Transaction';
						if($error_import > 0){
							$complete_message .= ', Error '.$error_import.' Transaction';
						}
					}else{
						$error_message = 'Import Future Error All '.$error_import.' Transaction';
					}
				}else{$error_message .= 'Cannot Upload File. Please Contact Administrator';}
			}else{
				$error_message .= 'No Data in File';
			}
			//echo 'product_list => '; var_dump($product_list); echo '<br/><br/>';
			
			$ReportSession->ResultImport = $ResultImport;
			$ReportSession->complete_message = $complete_message;
			$ReportSession->error_message = $error_message;
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
		}
		
		
		if(isset($data['port_id']) and $data['port_id'] != ''){
			$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
			$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to'], $data['type']);
		}else{
			$ResultFuturePlan = array();
			$ResultOptionPlan = array();
		}
		
		if($mode == 'delete_future_plan'){
			$count_delete = 0;
			if(isset($data['check1']) and $data['check1'] == 'Yes'){
				
			}else{
				
			}				
			if(isset($data['id']) and !empty($data['id'])){
				foreach($data['id'] as $future_id){
					$FuturePlanTable->ProcessDeleteFuturePlan($future_id);
					$count_delete++;
				}
			}else{
			}
			if($count_delete>0){
				$complete_message = 'Delete completed '.$count_delete.' transactions';
			}
			$ReportSession->complete_message = $complete_message;
			$ReportSession->error_message = $error_message;
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
		}
		
		if($mode == 'delete_option_plan'){
			$count_delete = 0;
			if(isset($data['check1']) and $data['check1'] == 'Yes'){
				
			}else{
				
			}				
			if(isset($data['id']) and !empty($data['id'])){
				foreach($data['id'] as $option_id){
					echo 'delete option id => '.$option_id.'<br/>';
					$OptionPlanTable->ProcessDeleteOptionPlan($option_id);
					$count_delete++;
				}
			}else{
			}
			if($count_delete>0){
				$complete_message = 'Delete completed '.$count_delete.' transactions';
			}
			$ReportSession->complete_message = $complete_message;
			$ReportSession->error_message = $error_message;
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
		}
		
		if($mode == 'add_future_plan'){
			$complete_import = 0;
			$error_import = 0;
			$ResultImport = array();
			if(isset($data['future_date']) and !empty($data['future_date'])){
				foreach($data['future_date'] as $id => $future_date){
					$error = 0;
					$error_detail = '';
					$UpdateFuture = array();
					$UpdateFuture['port_id'] = $data['port_id'];
					$UpdateFuture['transaction_id'] = $data['future_transaction_id'][$id];
					$UpdateFuture['date'] = $data['future_date'][$id];
					$UpdateFuture['symbol'] = $data['future_symbol'][$id];
					$UpdateFuture['price'] = $data['future_price'][$id];
					$UpdateFuture['amount'] = $data['future_amount'][$id];
					
					if(!isset($UpdateFuture['transaction_id']) || $UpdateFuture['transaction_id'] == ''){
					}else{
						$ResultDup = $FuturePlanTable->CheckDuplicate('', $data['port_id']. $UpdateFuture['transaction_id']);
						if($ResultDup){
							$error++;
							$error_detail .= 'Duplicate Transaction ID, ';
						}
					}
					
					if($error == 0){
						$UpdateFuture['port_id'] = $data['port_id'];
						$FuturePlanTable->AddFuturePlan($UpdateFuture);
						$UpdateFuture['transaction'] = $UpdateFuture['transaction_id'];
						$ResultImport[] = $UpdateFuture;
						$complete_import++;
					}else{
						$error_import++;
					}
				}
				if($complete_import > 0){
					$complete_message = 'Import Future Complete '.$complete_import.' Transaction';
					if($error_import > 0){
						$complete_message .= ', Error '.$error_import.' Transaction';
					}
				}else{
					$error_message = 'Import Future Error All '.$error_import.' Transaction';
				}
			}
			
			$ReportSession->ResultImport = $ResultImport;
			$ReportSession->complete_message = $complete_message;
			$ReportSession->error_message = $error_message;
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
		}
		
		if($mode == 'add_option_plan'){
			$complete_import = 0;
			$error_import = 0;
			$ResultImport = array();
			
			if(isset($data['option_date']) and !empty($data['option_date'])){ 
				foreach($data['option_date'] as $id => $option_date){
					$error = 0;
					$error_detail = '';
					
					$UpdateOption = array();
					$UpdateOption['date'] = $data['option_date'][$id];
					$UpdateOption['symbol'] = $data['option_symbol'][$id];
					$UpdateOption['type'] = $data['option_type'][$id];
					$UpdateOption['price'] = $data['option_price'][$id];
					$UpdateOption['premium'] = $data['option_premium'][$id];
					$UpdateOption['amount'] = $data['option_amount'][$id];
					
					if(!isset($UpdateOption['transaction_id']) || $UpdateOption['transaction_id'] == ''){
					}else{
						$ResultDup = $OptionPlanTable->CheckDuplicate('', $data['port_id'], $UpdateOption['transaction_id']);
						if($ResultDup){
							$error++;
							$error_detail .= 'Duplicate Transaction ID, ';
						}
					}
					
					if($error == 0){
						$UpdateOption['port_id'] = $data['port_id'];
						$OptionPlanTable->AddOptionPlan($UpdateOption);
						$UpdateOption['transaction'] = $UpdateOption['transaction_id'];
						$ResultImport[] = $UpdateOption;
						$complete_import++;
					}else{
						$error_import++;
					}
				}
			}
			
			$ReportSession->ResultImport = $ResultImport;
			$ReportSession->complete_message = $complete_message;
			$ReportSession->error_message = $error_message;
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
		}
		
		
		$ReportSession->data = $data;
		
		if(!isset($data['page1'])){$data['page1'] = 1;}
		if(!isset($data['per_page1'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page1'] = $ReportSession->per_page1;
			}else{
				$data['per_page1'] = 20;
			}
		}
		if($data['per_page1'] == ''){$data['per_page1'] = 20;}
		
		if(!isset($data['page2'])){$data['page2'] = 1;}
		if(!isset($data['per_page2'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page2'] = $ReportSession->per_page1;
			}else{
				$data['per_page2'] = 20;
			}
		}
		if($data['per_page2'] == ''){$data['per_page2'] = 20;}
		
		if($mode == 'Changepage'){
			$page_name = $data['change_page_name'];
			$page_value = $data['change_page_value'];
			$data[$page_name] = $page_value;
		}
	   
		$ReportSession->per_page1 = $data['per_page1'];
        $paginator_future1 = new Paginator(new ArrayAdapter($ResultFuturePlan));
        $paginator_future1->setCurrentPageNumber($data['page1'])
        ->setItemCountPerPage($data['per_page1'])
        ;
		$future_page_slide = $this->GeneratePaginationSlide($paginator_future1, $data['page1'], $data['per_page1'], 'page1');
		
		$ReportSession->per_page2 = $data['per_page2'];
        $paginator_option1 = new Paginator(new ArrayAdapter($ResultOptionPlan));
        $paginator_option1->setCurrentPageNumber($data['page2'])
        ->setItemCountPerPage($data['per_page2'])
        ;
		$option_page_slide = $this->GeneratePaginationSlide($paginator_option1, $data['page2'], $data['per_page2'], 'page2');
		
		
        return new ViewModel(array(            
            'baseUrl' => $this->getBaseUrl(),
            'data' => $data,
            'ResultImport' => $ResultImport,
            'ResultPort' => $ResultPort,
            'ResultIndex' => $ResultIndex,
            'ResultFuturePlan' => $ResultFuturePlan,
            'ResultOptionPlan' => $ResultOptionPlan,
            'complete_message' => $complete_message,
            'error_message' => $error_message,
            'paginator_future1' => $paginator_future1,
            'paginator_option1' => $paginator_option1,
			'lang' => $this->Language,
			'future_page_slide' => $future_page_slide,
			'option_page_slide' => $option_page_slide,
        )); 
		
	}
	
	public function editStockSettingAction()
    {
		$ReportSession = new Container('ReportSession');
		$PortTable = $this->getServiceLocator()->get('PortTable');
		$IndexTable = $this->getServiceLocator()->get('IndexTable');
		$SymbolTable = $this->getServiceLocator()->get('SymbolTable');
		$FuturePlanTable = $this->getServiceLocator()->get('FuturePlanTable');
		$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		//$FuturePlanTable = new Container('FuturePlanTable');
		//$OptionPlanTable = $this->getServiceLocator()->get('OptionPlanTable');
		
		$clear_sec = $this->params()->fromRoute('clear_sec');
        if($clear_sec != ''){
            $ReportSession->getManager()->getStorage()->clear('ReportSession');  
			$ReportSession->data = array('mode'=>'start_plan');
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'stock-setting',)); 
            exit();
        }
		
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
        }else{
			if(isset($ReportSession->data)){
				$data = $ReportSession->data;
			}else{
				$data = array();    
			}			    
        }		
		if(!isset($data['show_tabs'])){$data['show_tabs'] = '#future_plan';}
		if(!isset($data['port_id'])){$data['port_id'] = '';}
		if(!isset($data['port_id2'])){$data['port_id2'] = '';}
		if(!isset($data['index_id'])){$data['index_id'] = '';}
		if(!isset($data['type'])){$data['type'] = '';}
		if(!isset($data['date_from'])){$data['date_from'] = '';}
		if(!isset($data['date_to'])){$data['date_to'] = '';}
		if(!isset($data['future_contact'])){$data['future_contact'] = '';}
		if(!isset($data['option_contact'])){$data['option_contact'] = '';}
		if(!isset($data['range_from'])){$data['range_from'] = '900';}
		if(!isset($data['range_to'])){$data['range_to'] = '1300';}
		if(!isset($data['offset'])){$data['offset'] = '1';}
		
		$complete_message = '';
        $error_message = '';
		
		if(isset($ReportSession->complete_message) and $ReportSession->complete_message != ''){
			$complete_message = $ReportSession->complete_message;
			$ReportSession->complete_message = '';
		}
		
		if(isset($ReportSession->error_message) and $ReportSession->error_message != ''){
			$error_message = $ReportSession->error_message;
			$ReportSession->error_message = '';
		}
		
		//print_r($data); echo "<br><br>";
		$ResultPort = $PortTable->getPort('');
		$ResultIndex = $IndexTable->getIndex('');
		$ResultSymbol = $SymbolTable->getSymbol('', $data['index_id']);
		
		if(isset($ReportSession->ResultImport)){
			$ResultImport = $ReportSession->ResultImport;
			$ReportSession->ResultImport = array();
		}else{
			$ResultImport = array();
		}
		
		$mode = $this->getRequest()->getPost('mode');
		
		if($mode == 'save_stock_future'){
			foreach($data['id'] as $future_id){
				$UpdateFuture = array();
				$UpdateFuture['date'] = $data['future_date'][$future_id];
				$UpdateFuture['transaction_id'] = $data['future_transaction_id'][$future_id];
				$UpdateFuture['symbol'] = $data['future_symbol'][$future_id];
				$UpdateFuture['price'] = $data['future_price'][$future_id];
				$UpdateFuture['amount'] = $data['future_amount'][$future_id];
				$FuturePlanTable->UpdateFuturePlan($UpdateFuture,$future_id);	
			}
			$ReportSession->complete_message = 'Update Completed';
			$mode = 'start_edit_stock';
			//return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'edit-stock-setting',)); 
		}
		
		if($mode == 'save_stock_option'){
			foreach($data['id'] as $option_id){
				$UpdateOption = array();
				$UpdateOption['date'] = $data['option_date'][$option_id];
				$UpdateOption['transaction_id'] = $data['option_transaction_id'][$option_id];
				$UpdateOption['symbol'] = $data['option_symbol'][$option_id];
				$UpdateOption['type'] = $data['option_type'][$option_id];
				$UpdateOption['price'] = $data['option_price'][$option_id];
				$UpdateOption['premium'] = $data['option_premium'][$option_id];
				$UpdateOption['amount'] = $data['option_amount'][$option_id];
				$OptionPlanTable->UpdateOptionPlan($UpdateOption,$option_id);		
			}
			$ReportSession->complete_message = 'Update Completed';
			$mode = 'start_edit_stock';
			
		}
		
		
		if($mode == 'start_edit_stock'){
			if(isset($data['port_id']) and $data['port_id'] != ''){
				$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to'], $data['type']);
			}else{
				$ResultFuturePlan = array();
				$ResultOptionPlan = array();
			}
		
			if(isset($data['check1']) and $data['check1'] == 'Yes'){
				
			}elseif(isset($data['id']) and !empty($data['id'])){
				if($data['show_tabs'] == '#option_plan'){
					$ResultOptionPlan = $OptionPlanTable->getOptionPlan($data['id'], '', '', '', '', '');
				}
				
				if($data['show_tabs'] == '#future_plan'){
					$ResultFuturePlan = $FuturePlanTable->getFuturePlan($data['id'], '', '', '', '');
				}
			}else{
			}
			
			$ReportSession->ResultFuturePlan = $ResultFuturePlan;
			$ReportSession->ResultOptionPlan = $ResultOptionPlan;
			return $this->redirect()->toRoute('reports/default', array('controller'=>'reports', 'action' => 'edit-stock-setting',)); 
		}
		
		if($mode == 'change_value'){
			if(isset($data['port_id']) and $data['port_id'] != ''){
				$ResultFuturePlan = $FuturePlanTable->getFuturePlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to']);
				$ResultOptionPlan = $OptionPlanTable->getOptionPlan('', $data['port_id'], $data['index_id'], $data['date_from'], $data['date_to'], $data['type']);
			}else{
				$ResultFuturePlan = array();
				$ResultOptionPlan = array();
			}
			$ReportSession->ResultFuturePlan = $ResultFuturePlan;
			$ReportSession->ResultOptionPlan = $ResultOptionPlan;
		}
		
		$ResultFuturePlan = $ReportSession->ResultFuturePlan;
		$ResultOptionPlan = $ReportSession->ResultOptionPlan;
		
		$ReportSession->data = $data;
		
		if(!isset($data['page1'])){$data['page1'] = 1;}
		if(!isset($data['per_page1'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page1'] = $ReportSession->per_page1;
			}else{
				$data['per_page1'] = 20;
			}
		}
		if($data['per_page1'] == ''){$data['per_page1'] = 20;}
		
		if(!isset($data['page2'])){$data['page2'] = 1;}
		if(!isset($data['per_page2'])){
			if(isset($ReportSession->per_page1)){
				$data['per_page2'] = $ReportSession->per_page1;
			}else{
				$data['per_page2'] = 20;
			}
		}
		if($data['per_page2'] == ''){$data['per_page2'] = 20;}
		
		if($mode == 'Changepage'){
			$page_name = $data['change_page_name'];
			$page_value = $data['change_page_value'];
			$data[$page_name] = $page_value;
		}
	   
		$ReportSession->per_page1 = $data['per_page1'];
        $paginator_future1 = new Paginator(new ArrayAdapter($ResultFuturePlan));
        $paginator_future1->setCurrentPageNumber($data['page1'])
        ->setItemCountPerPage($data['per_page1'])
        ;
		$future_page_slide = $this->GeneratePaginationSlide($paginator_future1, $data['page1'], $data['per_page1'], 'page1');
		
		$ReportSession->per_page2 = $data['per_page2'];
        $paginator_option1 = new Paginator(new ArrayAdapter($ResultOptionPlan));
        $paginator_option1->setCurrentPageNumber($data['page2'])
        ->setItemCountPerPage($data['per_page2'])
        ;
		$option_page_slide = $this->GeneratePaginationSlide($paginator_option1, $data['page2'], $data['per_page2'], 'page2');
		
		
        return new ViewModel(array(            
            'baseUrl' => $this->getBaseUrl(),
            'data' => $data,
            'ResultImport' => $ResultImport,
            'ResultPort' => $ResultPort,
            'ResultIndex' => $ResultIndex,
            'ResultFuturePlan' => $ResultFuturePlan,
            'ResultOptionPlan' => $ResultOptionPlan,
            'complete_message' => $complete_message,
            'error_message' => $error_message,
            'paginator_future1' => $paginator_future1,
            'paginator_option1' => $paginator_option1,
			'lang' => $this->Language,
			'future_page_slide' => $future_page_slide,
			'option_page_slide' => $option_page_slide,
        )); 
		
	}
	
	
}

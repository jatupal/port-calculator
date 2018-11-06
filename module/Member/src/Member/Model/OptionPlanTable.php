<?php 

namespace Member\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\Db\Sql\Sql;  // use for join table
use Zend\Db\Sql\Where; // use for join table
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Insert;
use Zend\Stdlib\ArrayUtils;
use Zend\Session\Container;

class OptionPlanTable extends AbstractTableGateway  implements ServiceLocatorAwareInterface{
	protected $table = 'option_plan';
    protected $user_id;
    protected $serviceLocator;
	
	public function __construct(Adapter $adapter){
		$this->adapter = $adapter;
		$this->initialize();
		$session = new Container('User');		
		
		if($session->offsetExists('user_id')){$this->user_id = $session->offsetGet('user_id');}else{$this->user_id = "";}
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
	    $this->serviceLocator = $serviceLocator;
	}
	
	public function getServiceLocator(){
	    return $this->serviceLocator;
	}
		
	public function getOptionPlan($option_id, $port_id, $transaction_id, $date_from, $date_to, $type='')
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table);
	       
		$where = new  Where();	
		$where = $where->equalTo('user_id', $this->user_id);
		if(!is_array($option_id)){$option_id = array($option_id);}
		if(array_filter($option_id)){
			$where = $where->in('option_id', $option_id);
		}
		if(!is_array($port_id)){$port_id = array($port_id);}
		if(array_filter($port_id)){
			$where = $where->in('port_id', $port_id);
		}
		if(!is_array($transaction_id)){$transaction_id = array($transaction_id);}
		if(array_filter($transaction_id)){
			$where = $where->nest();
			foreach($transaction_id as $transaction_id2){
				$where = $where->expression('LOWER(transaction_id) = ?', strtolower($transaction_id2));	
			}
			$where = $where->unnest();
		}
		
		if($date_from != '' and $date_to != ''){
		    $time_from = strtotime($date_from);
		    $time_to = strtotime($date_to);
		    $date_from2 = $date_from;
		    $date_to2 = $date_to;
		    if($time_to > $time_from){
		        $date_from = $date_from2;
		        $date_to = $date_to2;
		    }else{
		        $date_to = $date_from2;
		        $date_from = $date_to2;
		    }
		}		
		
		if($date_from != ''){
            $where = $where->greaterThanOrEqualTo('date', $date_from.' 00:00:00');
		}
		if($date_to != ''){
		    $where = $where->lessThanOrEqualTo('date', $date_to.' 23:59:59');
		}
		if($type != ''){
			$where = $where->expression('LOWER(type) = ?', strtolower($type));	
		}
		
		$select->where($where);		
		//echo $sql->getSqlstringForSqlObject($select).'<BR><BR>';
	    //return;
		$statement = $sql->prepareStatementForSqlObject($select);
		$result = $this->resultSetPrototype->initialize($statement->execute())->toArray();
		
		return $result;
	}
	
	public function AddOptionPlan($data)
	{
		$data['user_id'] = $this->user_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		
		if(!$this->insert($data)){
	        return 'Fail to Insert';
	    }else{
			$option_id = $this->getLastInsertValue();
			if(!isset($data['transaction_id']) || $data['transaction_id'] == ''){
				$this->genTransaction($option_id);
			}
	        return $option_id;
	    }
	}
	
	public function genTransaction($option_id)
	{
		$ResultOption = $this->getOptionPlan($option_id, '', '', '', '');
		if(!empty($ResultOption)){			
			$TransactionId = "OP".$option_id;			
			
			$UpdateData = array();
			$UpdateData["transaction_id"] = $TransactionId;
			$this->UpdateOptionPlan($UpdateData,$option_id);
			return $TransactionId;
		}else{return $option_id;}
	}
	
	public function UpdateOptionPlan($data,$option_id)
    {   
		if($this->update($data, array('option_id'=>$option_id))){
            return 'Update Complete';
        }else{
            return 'Cannot Update';
        }
    }
	
	public function ProcessDeleteOptionPlan($option_id)
	{		
		$this->delete(array('option_id' => $option_id));
	}
	
	public function CalculateOptionPlan($check_price, $type, $strick_price, $premium, $amount)
	{
		$payoff = 0;
		//echo 'type => '.$type.'<br/>';
		if($type == 'Put'){
			$receive = $strick_price - $check_price;
			if($receive < 0){$receive = 0;}
			$payoff = ($amount*$receive)-($amount*$premium);
		}
		
		if($type == 'Put2'){
			$receive = $check_price-$strick_price;
			if($receive < 0){$receive = 0;}
			$payoff = ($amount*$premium)-($amount*$receive);
		}
		
		if($type == 'Call'){
			$receive = $check_price-$strick_price;
			if($receive < 0){$receive = 0;}
			$payoff = ($amount*$receive)-($amount*$premium);
		}
		
		return $payoff;
	}
	
	public function CheckDuplicate($option_id, $port_id, $transaction_id)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table);
	       
		$where = new  Where();	
		$where = $where->equalTo('user_id', $this->user_id);
		if($port_id != ''){
			$where = $where->equalTo('port_id', $port_id);
		}
		$where = $where->expression('LOWER(transaction_id) = ?', strtolower($transaction_id));		
		
		
		$select->where($where);		
		//echo $sql->getSqlstringForSqlObject($select).'<BR><BR>';
	    //return;
		$statement = $sql->prepareStatementForSqlObject($select);
		$ResultSet = $this->resultSetPrototype->initialize($statement->execute())->toArray();
		
		if(!empty($ResultSet)){			
			$option_id = (int)$option_id;
			$ResultSet[0]['option_id'] = (int)$ResultSet[0]['option_id'];
			if($ResultSet[0]['option_id'] == $option_id){
				return false;
			}else{
				return true;
			}			
		}else{
			return false;
		}
		
		return $result;
	}
	
	
	
}

?>
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

class PortTable extends AbstractTableGateway  implements ServiceLocatorAwareInterface{
	protected $table = 'portferio';
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
		
	public function getPort($port_id)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table);
	       
		$where = new  Where();	
		$where = $where->equalTo('user_id', $this->user_id);
		if(!is_array($port_id)){$port_id = array($port_id);}
		if(array_filter($port_id)){
			$where = $where->in('port_id', $port_id);
		}		
		
		$select->where($where);		
		//echo $sql->getSqlstringForSqlObject($select).'<BR><BR>';
	    //return;
		$statement = $sql->prepareStatementForSqlObject($select);
		$result = $this->resultSetPrototype->initialize($statement->execute())->toArray();
		
		return $result;
	}
	
	public function AddPort($data)
	{
		$data['user_id'] = $this->user_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		
		if(!$this->insert($data)){
	        return 'Fail to Insert';
	    }else{
			$port_id = $this->getLastInsertValue();
	        return $port_id;
	    }
	}
	
	public function UpdatePort($data,$port_id)
    {   
		if($this->update($data, array('port_id'=>$port_id))){
            return 'Update Complete';
        }else{
            return 'Cannot Update';
        }
    }
	
	public function ProcessDeletePort($port_id)
	{		
		$FuturePlanTable = $this->serviceLocator->get('FuturePlanTable');
		$OptionPlanTable = $this->serviceLocator->get('OptionPlanTable');
			
		$this->DeletePort($port_id); // Delete Port
		$FuturePlanTable->DeleteFuturePlan($port_id, ''); //Delete Future Plan
		$OptionPlanTable->DeleteOptionPlan($port_id, ''); //Delete Option Plan
			
		return true;
	}
	
	private function DeletePort($port_id){
		$this->delete(array('port_id' => $port_id));
	}
	
	
}

?>
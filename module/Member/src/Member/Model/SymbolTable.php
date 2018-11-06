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

class SymbolTable extends AbstractTableGateway  implements ServiceLocatorAwareInterface{
	protected $table = 'symbol';
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
		
	public function getSymbol($symbol_id, $index_id)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table);
	       
		$where = new  Where();	
		$where = $where->equalTo('user_id', $this->user_id);
		if(!is_array($symbol_id)){$symbol_id = array($symbol_id);}
		if(array_filter($symbol_id)){
			$where = $where->in('symbol_id', $symbol_id);
		}
		if(!is_array($index_id)){$index_id = array($index_id);}
		if(array_filter($index_id)){
			$where = $where->in('index_id', $index_id);
		}		
		
		$select->where($where);		
		//echo $sql->getSqlstringForSqlObject($select).'<BR><BR>';
	    //return;
		$statement = $sql->prepareStatementForSqlObject($select);
		$result = $this->resultSetPrototype->initialize($statement->execute())->toArray();
		
		return $result;
	}
	
	public function AddSymbol($data)
	{
		$data['user_id'] = $this->user_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		
		if(!$this->insert($data)){
	        return 'Fail to Insert';
	    }else{
			$symbol_id = $this->getLastInsertValue();
	        return $symbol_id;
	    }
	}
	
	public function UpdateSymbol($data,$symbol_id)
    {   
		if($this->update($data, array('symbol_id'=>$symbol_id))){
            return 'Update Complete';
        }else{
            return 'Cannot Update';
        }
    }
	
	public function ProcessDeleteSymbol($symbol_id)
	{		
		$this->delete(array('symbol_id' => $symbol_id));
	}
	
	
	
	
}

?>
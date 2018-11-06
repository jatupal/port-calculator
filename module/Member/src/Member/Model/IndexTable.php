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

class IndexTable extends AbstractTableGateway  implements ServiceLocatorAwareInterface{
	protected $table = 'index';
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
		
	public function getIndex($index_id)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table);
	       
		$where = new  Where();	
		$where = $where->equalTo('user_id', $this->user_id);
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
	
	public function AddIndex($data)
	{
		$data['user_id'] = $this->user_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		
		if(!$this->insert($data)){
	        return 'Fail to Insert';
	    }else{
			$index_id = $this->getLastInsertValue();
	        return $index_id;
	    }
	}
	
	public function UpdateIndex($data,$index_id)
    {   
		if($this->update($data, array('index_id'=>$index_id))){
            return 'Update Complete';
        }else{
            return 'Cannot Update';
        }
    }
	
	public function ProcessDeleteIndex($index_id)
	{		
		$this->delete(array('index_id' => $index_id));
	}
	
	
}

?>
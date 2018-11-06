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
use Zend\Db\Sql\Expression;

class UserTable extends AbstractTableGateway  implements ServiceLocatorAwareInterface{
	protected $table = 'user';
	protected $member_id;
    protected $user_id;
    protected $company_id;	
    protected $serviceLocator;
	
	public function __construct(Adapter $adapter){
		$this->adapter = $adapter;
		$this->initialize();
		$session = new Container('User');		
		
		if($session->offsetExists('user_id')){$this->user_id = $session->offsetGet('user_id');}else{$this->user_id = "";}
		if($session->offsetExists('member_id')){$this->member_id = $session->offsetGet('member_id');}else{$this->member_id = "";}
		if($session->offsetExists('company_id')){$this->company_id = $session->offsetGet('company_id');}else{$this->company_id = "";}
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
	    $this->serviceLocator = $serviceLocator;
	}
	
	public function getServiceLocator(){
	    return $this->serviceLocator;
	}
	
    public function checkUser($username, $password){
		$sql = new Sql($this->adapter);
	    $select = $sql->select();
	    $select->from($this->table);
		$where = new  Where();
		$where = $where->expression('LOWER(username) = ?', strtolower($username));
		$where = $where->expression('password = ?', $password);
		$select->where($where);  
		//echo $sql->getSqlstringForSqlObject($select).'<BR><BR>';  
		$statement = $sql->prepareStatementForSqlObject($select);
    	$result = $this->resultSetPrototype->initialize($statement->execute())->toArray();
		//echo 'resultShipping => '; var_dump($resultShipping); echo '<br/><br/>';
		return $result;
	}
	
	public function getUsers($username)
	{
		$sql = new Sql($this->adapter);
	    $select = $sql->select();
	    $select->from($this->table);
	    //if(array_filter($data)){
	    
	    $where = new  Where();
	    $where = $where->expression('LOWER('.$this->table.'.username) = ?', strtolower($username));
		$select->where($where);
		//echo $sql->getSqlstringForSqlObject($select).'<BR><BR>'; return;
		$statement = $sql->prepareStatementForSqlObject($select);
	    $ResultSet = $this->resultSetPrototype->initialize($statement->execute())->toArray();
        return $ResultSet;
	}
	
	public function getUsersID($id)
	{
		$sql = new Sql($this->adapter);
		$select = $sql->select();
		$select->from($this->table);
	       
		$where = new  Where();	
		//$where = $where->equalTo('company_id', $this->company_id);
		if(!is_array($id)){$id = array($id);}
		if(array_filter($id)){
			$where = $where->equalTo('user_id', $id);
		}		
		
		$select->where($where);		
		//echo $sql->getSqlstringForSqlObject($select).'<BR><BR>';
	    //return;
		$statement = $sql->prepareStatementForSqlObject($select);
		$result = $this->resultSetPrototype->initialize($statement->execute())->toArray();
		
		return $result;
	}
	
	public function AddUser($data)
	{
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		
		if(!$this->insert($dataProduct)){
	        return 'Fail to Insert';
	    }else{
			$user_id = $this->getLastInsertValue();
	        return $user_id;
	    }
	}
	
	public function UpdateUser($data,$user_id)
    {   
		if($this->update($data, array('user_id'=>$user_id))){
            return 'Update Complete';
        }else{
            return 'Cannot Update';
        }
    }
	
	public function ProcessDeleteUser($user_id)
	{		
		$PortTable = $this->serviceLocator->get('PortTable');
			
		$this->DeleteUser($user_id); // Delete User
		$PortTable->DeletePort($port_id, ''); //Delete Port
			
		return true;
	}
	
	private function DeleteUser($user_id){
		$this->delete(array('user_id' => $user_id));
	}
	
	
	
	
	
	
	
}

?>
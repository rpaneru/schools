<?php
namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OauthAuthorizationCodesTable
{
    protected $dbAdapter;
    protected $tableGateway;
    
    public function __construct(Adapter $dbAdapter, TableGateway $tableGateway) 
    {
        $this-> dbAdapter = $dbAdapter;
        $this-> tableGateway = $tableGateway;
    }

    public function getOauthAuthorizationCodes($param)
    {        
        $sql = new Sql($this-> dbAdapter);
        $select = $sql-> select();
        $select-> columns(array('authorization_code'));  
        $select-> from(array('oac' =>'oauth_authorization_codes' ));  
        $select-> where($param);
        
        //echo 'query =>'.$select->getSqlString($this->tableGateway->getAdapter()->getPlatform()).'<br />';
        
        $statement = $sql-> prepareStatementForSqlObject($select);
        $result = $statement-> execute();
        $result = iterator_to_array($result);
        return $result[0];
    }
}

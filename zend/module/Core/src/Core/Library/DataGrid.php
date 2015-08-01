<?php
namespace Core\Library;

class DataGrid
{
	/**
	 * Model
	 */
	private $model;
	
	/**
	* Columns
	*/
	public $columns = array();
	
	/**
	* Joins
	*/
	public $joins = array();
	
	/**
	* Pre Filters
	*/
	public $pre_filters = array();
	
	/**
	* Query
	*/
	public $q;
	
	/**
	* Call back function which used to execute before query to execute.
	*/	
	private $beforeExecute_callback = false;

	
	/**
	 * Query Result data
	 */
	public $rd = array();
	
	/**
	* Parameters
	*/
	public $params = array();
	
	/**
	 * Constructor
	 *
	 * @param Zend\Db\TableGateway\TableGateway $model
	 * @param array $config
	 * @param array $data
	 */
	public function __construct(\Zend\Db\TableGateway\TableGateway $model, $config, $data)
	{
		$this->model = $model;
		$this->columns = array_key_exists('columns', $config)? $config['columns']: array();
		$this->joins = array_key_exists('joins', $config)? $config['joins']: array();
		$this->pre_filters = array_key_exists('pre-filters', $config)? $config['pre-filters']: array();
		$this->data = $data;
	}

	/**
	* Result data method
	*/
	public function processData()
	{
		$post = $this->data;
		$params = array(
			'pn' => 1,
			'rpp' => 200,
			'nor' => false,
			'nop' => false,
			'filters' => array(),
			'orderby' => null,
			'orderby_desc' => true,
			'searchTerm' => ''
		);
		foreach($params as $key => $value) {
			if(array_key_exists($key, $post)) {
				switch($key){
					case 'nop':
					case 'nor':
					case 'pn':
					case 'rpp':
						$params[$key] = (int) $post[$key];
					break;
					case 'orderby_desc':
						if($post[$key] == 'true') {
							$params[$key] = true;
						} else {
							$params[$key] = false;
						}
					break;
					default:
						$params[$key] = $post[$key];
					break;
				}
			}
		}
		$this->process($params);
	}
	
	public function process($params)
	{
		$columns = array();
		$filters = array();
		$search_or = array();
		//query-builder
		$select = $this->model->getSql()->select();
		//pre-filters
		for($i=0; $i<count($this->pre_filters); $i++) {
			if(array_key_exists('value', $this->pre_filters[$i])) {
				if(
					array_key_exists('join', $this->pre_filters[$i]) &&
					$this->pre_filters[$i]['join']
				) {
					//filter for joined tables
					$filters[$this->pre_filters[$i]['join']. '.' . $this->pre_filters[$i]['field']] = $this->pre_filters[$i]['value'];
				} else {
					//filter for main table
					$filters[ $this->model->getTable() . '.' .$this->pre_filters[$i]['field']] = $this->pre_filters[$i]['value'];
				}
			}
		}
		//parse
		for($i=0; $i<count($this->columns); $i++) {
			if(
				array_key_exists('field', $this->columns[$i]) &&
				$this->columns[$i]['field'] != ''
			) {
				$column = $this->columns[$i];
				if(
					array_key_exists('join', $column) &&
					array_key_exists($column['join'], $this->joins)
				) {
					$this->joins[$column['join']]['columns'][$column['join'] . '_' . $column['field']] = $column['field'];
				} else {
					$columns[] = $column['field'];
				}
				//search
				if($params['searchTerm']) {
					//escape the characters to match the regex.
					$match_str = array("(", ")", "[", "]", "+", "*");
					$replace_str =  array("\(", "\)","\[", "\]", "\+", "\*");
					$searchTerm = str_replace($match_str, $replace_str, $params['searchTerm']);
					//check if field is searchable
					if(
						array_key_exists('searchable', $column) &&
						$column['searchable']
					) {
						//$select->where->OR->like($this->columns[$i]['field'], "%".$searchTerm."%");
						if(
							array_key_exists('join', $column) &&
							$column['join']
						) {
							$search_or[$column['join']. '.' . $column['field']] = $searchTerm;
						} else {
							$search_or[$this->model->getTable() . '.' .$column['field']] = $searchTerm;
						}
					}
				}
				//filter
				if(count($params['filters'])) {
					if(
						array_key_exists('filterable', $column) && 
						$column['filterable']
					) {
						//filter for joined tables
						if(
							array_key_exists('join', $column) &&
							$column['join'] &&
							array_key_exists($column['join']. '_' . $column['field'], $params['filters'])
						) {
							$filters[$column['join']. '.' . $column['field']] = $params['filters'][$column['join']. '_' . $column['field']];
						} elseif(array_key_exists($column['field'], $params['filters'])) { //filter for main table
							$filters[ $this->model->getTable() . '.' .$column['field']] = $params['filters'][$column['field']];
						}
					}
				}
			}
		}
		//set columns
		$select->columns($columns);
		//joins
		foreach($this->joins as $name => $join) {
			if(! array_key_exists('on', $join)) {
				$join['alias'] = $name;
				$this->joins[$name]['on'] = $join['alias'] . '.id' . ' = ' . $this->model->getTable() . '.' . $join['fk'];
				$select->join(array(
					$join['alias'] => $join['table']
				), $this->joins[$name]['on'], $join['columns'], 'left');
			}
		}
		//add filters
		if(count($filters)) {
			foreach($filters as $key => $value) {
				if(is_array($value)) {
					$select->where->in($key, $value);
				} else {
					$select->where->equalTo($key, $value);
				}
			}
		}
		//add like clause for searchable's
		if(count($search_or)) {
			$nested_or = $select->where->nest();
			foreach($search_or as $key => $value) {
				$nested_or->OR->like($key, '%'.$value.'%');	
			}
			$nested_or->unnest();
		}
		//sorting
		//TO-DO: multi-field sorting
		$order = 'asc';
		if($params['orderby_desc']) {
			$order = 'desc';
		}
		if($params['orderby']) {
			$select->order(array($params['orderby'].' '.$order));
		}
		//before-execute
		if(is_callable($this->beforeExecute_callback)){
		    $select = call_user_func($this->beforeExecute_callback, $select);
		}
		//get-nor
		$params['nor'] = $this->model->selectWith($select)->count();
		if($params['rpp'] > $params['nor']) {
			$params['nop'] = 1;
		} else {
			$params['nop'] = ceil($params['nor'] / $params['rpp']);
		}
		//limit
		$select->limit($params['rpp']);
		$select->offset(($params['pn'] - 1) * $params['rpp']);
		$this->params = $params;
		//execute query
		$this->q = $this->model->getSql()->getSqlstringForSqlObject($select);
		$this->rd = $this->model->selectWith($select)->toArray();
	}
	
	/**
	* Setter for callback function used to execute before query to execute.
	*/
	public function beforeExecute($method)
	{
		$this->beforeExecute_callback = $method;
	}
}
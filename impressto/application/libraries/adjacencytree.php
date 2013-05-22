<?php

/**
* $Id: adjacency.class.php,v 1.2 2011/12/15 
*
*
* This is a simple class to handle adjacency list model operations within a MySQL database. 
* The class manages data trees based on the adjacency list  model.
* Please see: http://en.wikipedia.org/wiki/Adjacency_list
*
*
* Please see examples file for operation
*
* LICENSE: Redistribution and use in source and binary forms, with or
* without modification, are permitted provided that the following
* conditions are met: Redistributions of source code must retain the
* above copyright notice, this list of conditions and the following
* disclaimer. Redistributions in binary form must reproduce the above
* copyright notice, this list of conditions and the following disclaimer
* in the documentation and/or other materials provided with the
* distribution.
*
* THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED
* WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
* MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN
* NO EVENT SHALL CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
* OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
* TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
* USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
* DAMAGE.
*
* @package     Adjacency List Model
* @author      Galbraith Desmond <galbraithdesmond@gmail.com>
* @copyright   2010 Galbraith Desmond
* @license     http://www.opensource.org/licenses/bsd-license.php
*
* 
*/

class adjacencyTree{

	/**
	* @var used for mysql operations
	*/
	public $dbConnectionID;

	/**
	* @var array $refs holds a data reference to the child nodes
	*/
	public $dbtablename = '';
	
	/**
	* @var array $refs holds a data reference to the child nodes
	*/
	public $refs = array();
	
	/**
	* @var array $list holds a data reference to the parent node(s)
	*/
	public $list = array();

	/**
	* @var array $idlist holds a list of ids
	*/
	public $idlist = array();
	
	
	/**
	* @var array $includefields holds list of field names to retrieve
	*/
	public $includefields = null;
	
	
	/**
	* @var int used to set initial list key to parent id
	*/
	private $initparentid = null;

	/**
	* @var string $id_field points to the key id of the record
	*/
	public $id_field = '';

	/**
	* @var string $parent_id_field points to the key parent_id of the record
	*/
	public $parent_id_field = '';	
	
	
	/**
	* @var string $parent_field points to the key parent_id of the record
	*/
	//public $parent_field = '';
	
	
	
	/**
	* @var string $position_field points to the key position of the record
	*/
	public $position_field = '';	

	/**
	* @var array $tablefields holds all non key fields of the source data  table
	*/
	private $tablefields = array();	
	
	
	/**
	* @var string $sql_conditional adds a conditional to the query used to generate the master recordset
	*/
	public $sql_conditional = '';	
	
	
	
	/**
	* @var $jointable is the name of a table joined to the primary table
	*/
	private $jointable = null;
	
	/**
	* @var $jointables is an array containing tables to be joined to the primary table
	*/	
	private $jointables = array();
	
	
	/**
	* @var $joinkey is the field that will glue the joined table to the primary table
	*/
	private $joinkey = null;
	
	
	/**
	* @var array $join_includefields  holds any fields we want to pull from the joined table
	*/
	private $join_includefields = null;
	
	
	/**
	* @ bool $deletejoindata determines if we delete joined records when the node is removed
	*/
	private $deletejoindata = false;
	
	
	
	/**
	* @var array $deleteids holds a list of ids that can be deleted from a node
	*/
	private $deleteids = array();
	
	
	/**
	* @var $debug allows us to see sql strings
	*/
	public $debug = false;
	
	
	/**
	* Constructor
	* 
	*/
	public function __construct(){
		
	}//////////////
	
	
	/**
	* Reset the class to its default state
	* 
	*/
	public function init(){
	
		$this->dbtablename = '';
		$this->refs = array();
		$this->list = array();
		$this->idlist = array();
		$this->includefields = null;
		$this->initparentid = null;
		$this->id_field = '';
		$this->parent_id_field = '';	
		$this->position_field = '';	
		$this->tablefields = array();	
		$this->jointable = null;
		$this->joinkey = null;
		
		$this->jointables = array();
				
		$this->join_includefields = null;
		$this->deletejoindata = false;
		$this->deleteids = array();
		$this->debug = false;
	
		
	}//////////////
	
	
	/**
	*  set database connection id
	* @param string
	*/
	public function setDBConnectionID($dbconnectionid){

		$this->dbConnectionID = $dbconnectionid;
		
	}//////////////////////////
	
	
	
	/**
	*  set var id_field
	* @param string
	*/
	public function setidfield($id_field){
		
		$this->id_field = $id_field;
		
	}/////////////////

	
	/**
	*  set debug mode
	* @param bool
	*/
	public function setdebug($val){
		
		$this->debug = $val;
		
	}/////////////////

	
	
	/**
	*  set var parent_id_field
	* @param string
	*/
	public function setparentidfield($parent_id_field){
		
		$this->parent_id_field = $parent_id_field;
		
	}//////////////////

	/**
	*  set var includefields - minimized memory load from unnecessary fields
	* @param string
	*/
	public function setincludefields($includefields){

		if(is_array($includefields) && count($includefields) > 0){
			
			$this->includefields = $includefields;
			
			// nullify any joined tables
			$this->join_includefields = null;
		}
		
		
	}//////////////////
	
	
	
	/**
	*  set var position_field
	* @param string
	*/
	public function setpositionfield($position_field){
		
		$this->position_field = $position_field;
		
	}/////////////
	
	
	/**
	*  set var dbtablename
	* @param string
	*/
	public function setdbtable($tablename){
		
		$this->dbtablename = $tablename;
		
	}///////////////////
	
	
	function set_deletejoindata($state){
		
		$this->deletejoindata = $state;
		
	}
	
	function set_sql_conditional($string){
		
		$this->sql_conditional = $string;
		
	}	
	public function setjointable($jointable,$joinkey,$join_includefields){

		if(
				is_array($join_includefields) 
				&& count($join_includefields) > 0
				&& $jointable != ""
				&& $joinkey != ""
				){
			
			$this->jointable = $jointable;
			$this->joinkey = $joinkey;
			$this->join_includefields = $join_includefields;
			
			// now nullify includefields because they are no longer needed
			$this->includefields = null;
		}
	}
	
	/**
	* Allows the connection of additional table using the LEFT JOIN method
	*
	*/
	public function set_additional_jointable($jointable,$joinkey,$join_includefields){

		if(
				is_array($join_includefields) 
				&& count($join_includefields) > 0
				&& $jointable != ""
				&& $joinkey != ""
				){
			
			$this->jointables[] = array("table"=>$jointable,"key"=>$joinkey,"includefields"=>$join_includefields);
					
			$this->includefields = null;
		}
	}
	
	
	/**
	* Set the field names for the table omitting key fields
	*/
	private function setFieldNames(){
		
		$sql = "SHOW COLUMNS FROM " . $this->dbtablename;
		
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result)) {
			
			$fieldname = $row['Field'];
			
			if( $fieldname != $this->id_field
					&& $fieldname != $this->position_field
					){
				$this->tablefields[] = $fieldname;
			}
			
		}
		
	}//////////////////
	
	
	/**
	* trigger for getFullNodesArray resets list and ref before call
	* @return multidimensional array
	*/
	public function getFullNodes(){
		
		$this->resetdrill = false;
		$this->list = array();  // reinitialize this list
		$this->refs = array();  // reinitialize this list
		$this->initparentid = null; // init all
		
		return $this->getFullNodesArray();
		
	}/////////////////
	
	
	/**
	* read all data from the source able and put it into a multidimensional array 
	* @return multidimensional array
	*/
	public function getFullNodesArray(){
		
		$sql = $this->build_select_query() . $this->build_from_query();
		

		if($this->sql_conditional != "") $sql .=  " WHERE ("  . $this->sql_conditional . ") ";
		
		$sql .= " ORDER BY {$this->dbtablename}.{$this->position_field}";
		
		if($this->debug) echo $sql;
		
		
		
		$result = mysql_query($sql);
		
		
		while($row = @mysql_fetch_assoc($result)) {
			
			$thisref = &$this->refs[ $row[$this->id_field] ];

			$thisref[$this->parent_id_field] = $row[$this->parent_id_field];
			
			$thisref[$this->position_field] = $row[$this->position_field];
			
			if(is_array($this->join_includefields)){
				
				foreach($this->join_includefields as $val){
					
					$thisref[$val] = $row[$val];
					
				}
				
				// phasing in for version 3 - Aug 31, 2012
				if(is_array($this->jointables) && count($this->jointables) > 0 ){
			
					
					foreach($this->jointables AS $jointable){
				
						if(is_array($jointable['includefields']) && count($jointable['includefields']) > 0){
					
							foreach($jointable['includefields'] AS $val){
										
								$thisref[$val] = $row[$val];
							
							}
						
						}
						
					}
					
				}
				
				
			}else if(is_array($this->includefields)){
				
				foreach($this->includefields as $val){
					$thisref[$val] = $row[$val];
				}	
				
			}else{
				
				foreach($this->tablefields as $val){
					$thisref[$val] = $row[$val];
				}		
				
				
			}

			
			if ($row[$this->parent_id_field] == 1) {
				$this->list[ $row[$this->id_field] ] = &$thisref;								
			} else {
				$this->refs[ $row[$this->parent_id_field] ]['children'][ $row[$this->id_field] ] = &$thisref;
			}
		}
		
		
		
		return $this->list;
		
	}////////////////////
	

	/**
	* Not used yet but may come in useful later
	*/
	public function getFullNodesIDs($parent) {
		
		$items = $this->getFullNodesArray($parent);
		
		return $this->getNodeIds($items);
		

		
	}////////// 

	
	/**
	* Return all the ids of the children
	*@param $parent_id
	*@return array
	*/
	public function getChildNodeIDs($parent) {
		
		
		$this->list = array();  // reinitialize this list
		$this->refs = array();  // reinitialize this list
		$this->initparentid = null; // init all
		
		$items = $this->getChildNodesArray($parent);
		
		return $this->getNodeIds($items);
		

		
	}////////// 
	
	
	/*
	*  gets the nodes for a sibling , trigger for getChildNodesArray resets list and ref before call
	* @return multidimensional array
	*/
	public function getSiblingNodes($sibling_id) {
		
		$this->list = array();  // reinitialize this list
		$this->refs = array();  // reinitialize this list
		$this->initparentid = null; // init all
		
		$parent_id = $this->getParentID($sibling_id);
		
		return $this->getChildNodesArray($parent_id);
		
		
	}//////////////
	

	/*
	* trigger for getChildNodesArray resets list and ref before call
	* @return multidimensional array
	*/
	public function getChildNodes($parent) {
		
		$this->list = array();  // reinitialize this list
		$this->refs = array();  // reinitialize this list
		$this->initparentid = null; // init all
		
		return $this->getChildNodesArray($parent);
		
		
	}//////////////


	/*
	* return the parent id of the selected category
	*@param $node_id
	*@return $parent_id
	*/
	public function getParentID($node_id) {
		
		$sql = "SELECT {$this->dbtablename}.{$this->parent_id_field} FROM {$this->dbtablename} WHERE {$this->dbtablename}.{$this->id_field} = '$node_id' ";

		$row = mysql_fetch_row(mysql_query($sql));

		return $row[0];
		
	}

	

	
	/*
	* return the immediate parent data of the selected category
	*@param $node_id
	*@return $array of parent data
	*/
	public function getParentData($node_id) {
		
		$row = null;
		
		$parent_id = $this->getParentID($node_id);
		
		$sql = $this->build_select_query() . $this->build_from_query();

		$sql .= " WHERE {$this->dbtablename}.{$this->id_field} = '{$parent_id}' ";
	
		$result = mysql_query($sql);
		
		if($result) $row = mysql_fetch_assoc($result);

		return $row;


	}
	
	
	/*
	* return an enumerated array of parent ids for the selected child
	*@param $node_id
	*@return array list of full path parent_ids
	*/
	public function getParentIDs($node) {

		$ajdparent_ids = array(); // initialize the idlist
		
		$has_parent = true;
		
		while($has_parent){
			
			$node = $this->getParentID($node);
			
			if($node > 1){ // over the master id
				
				$ajdparent_ids[] = $node;
				
			}else{
				
				$has_parent = false;
			}	
		}	
		
		
		$ajdparent_ids = array_reverse($ajdparent_ids);
		
		
		return $ajdparent_ids;
		
	}/////////////
	
	
	/*
	* return an enumerated array of parent data for the selected child
	*@param $node_id
	*@return array list of full path parents data
	*/
	public function getParentsData($node_id) {

		$ajdparent_data = array(); // initialize the idlist
		
		$nodedata[$this->id_field] = $node_id;
				
		$has_parent = true;
		
		while($has_parent){
		
		
			$nodedata = $this->getParentData($nodedata[$this->id_field]);

		
			if($nodedata[$this->id_field] > 1){ // over the master id
				
				$ajdparent_data[] = $nodedata;
				
			}else{
				
				$has_parent = false;
			}	
			
		}				
		
		return array_reverse($ajdparent_data);

		
	}/////////////
	
	

	
	/**
	* Get all the position info required to determine where a category is in terms of the beginning or end of a list
	* Useful if you want to add move up/down arrows
	*@param int node_id
	*@return array
	*/
	public function getPositionData($node_id) {

		$cat_count = 0;
		$found_position = null;

		
		$parent_id = $this->getParentID($node_id);

		
		$sql = "SELECT ";
		
		$sql = " {$this->dbtablename}.{$this->id_field} ";
		
		
		$sql .= " FROM {$this->dbtablename} WHERE {$this->dbtablename}.{$this->parent_id_field} = '$parent_id' ";
		
		if($this->sql_conditional != "") $sql .=  " AND ("  . $this->sql_conditional . ") ";
		
		$sql .= " ORDER BY {$this->dbtablename}.{$this->position_field}";
		

		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result)) {
			
			$this_id = $row[$this->id_field];
			
			if($node_id == $this_id){
				
				$found_position = $cat_count;
				
			}
			
			$cat_count ++;
			
		}	

		return array("parent_id"=>$parent_id,"numcats"=>($cat_count-1),"position"=>$found_position);
		
		
	}//////////////

	
	
	/**
	* Moves a node up or down within the parent list
	*@params $node_id, $direction
	*@return null
	*/
	public function repositionSibling($node_id,$direction){
		
		
		$direction = strtolower($direction);
		
		$parent_id = $this->getParentID($node_id);
		
		// first thing is to clean gaps and set positions for the siblings
		$positioncount = 0;
		
		$sql = "SELECT ";
		
		$sql .= " {$this->dbtablename}.{$this->id_field} ";
		
		
		$sql .= " FROM {$this->dbtablename} WHERE {$this->dbtablename}.{$this->parent_id_field} = '$parent_id' ";
		
		if($this->sql_conditional != "") $sql .=  " AND ("  . $this->sql_conditional . ") ";
				
		$sql .= " ORDER BY {$this->dbtablename}.{$this->position_field} ASC";
		
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result)) {
			
			$this_id = $row[$this->id_field];
			
			$sql = "UPDATE {$this->dbtablename} SET {$this->dbtablename}.{$this->position_field}='$positioncount' WHERE {$this->dbtablename}.{$this->id_field} = '$this_id'";
			
			mysql_query($sql);
			
			$positioncount ++;
			
		}

		
		$sql = "SELECT ";
		
		$sql .= " {$this->dbtablename}.{$this->id_field}, {$this->dbtablename}.{$this->position_field} ";
		
		$sql .= " FROM {$this->dbtablename} WHERE {$this->dbtablename}.{$this->parent_id_field} = '$parent_id' ";
		
		if($this->sql_conditional != "") $sql .=  " AND ("  . $this->sql_conditional . ") ";
		
		$sql .= " ORDER BY {$this->dbtablename}.{$this->position_field}";
		
		
		if($direction == "up")	$sql .= " ASC";
		else $sql .= " DESC";		
		
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result)) {
			
			$this_id = $row[$this->id_field];
			$this_position = $row[$this->position_field];
			
			
			if( $this_id == $node_id){
				
				// we need to get the previous id and position
				$sql = "UPDATE {$this->dbtablename} SET {$this->dbtablename}.{$this->position_field}='{$prev_position}' WHERE {$this->dbtablename}.{$this->id_field} = '$this_id'";
				mysql_query($sql);
				
				$sql = "UPDATE {$this->dbtablename} SET {$this->dbtablename}.{$this->position_field}='{$this_position}' WHERE {$this->dbtablename}.{$this->id_field} = '$prev_id'";
				mysql_query($sql);						
				
			}
			
			$prev_id = $this_id;
			$prev_position = $this_position;
			
			
		}		
		
		
	}////////////
	
	
	/**
	* Get all the nodes that have the same parent .. pretty basic
	*/
	public function getSiblings($node_id) {
		
		
		
		$siblings = array();
		
		$parent_id = $this->getParentID($node_id);
		
		$sql = $this->build_select_query() . $this->build_from_query();

		$sql .= " WHERE {$this->dbtablename}.{$this->parent_id_field}='{$parent_id}' AND {$this->dbtablename}.{$this->id_field } != '$node_id' ";
		
		if($this->sql_conditional != "") $sql .=  " AND ("  . $this->sql_conditional . ") ";
				
		$sql .= " ORDER BY {$this->dbtablename}.{$this->position_field}";

		$result = mysql_query($sql);
		
		
		while ($row = mysql_fetch_array($result)) {
			
			
			$thisref = array($this->id_field=>$row[$this->id_field]);
			
			$thisref[$this->parent_id_field] = $row[$this->parent_id_field];
			$thisref[$this->position_field] = $row[$this->position_field];
			
			if(is_array($this->join_includefields)){
				
				foreach($this->join_includefields as $val){
					
					$thisref[$val] = $row[$val];
					
				}
				
				// phasing in for version 3 - Aug 31, 2012
				if(is_array($this->jointables) && count($this->jointables) > 0 ){
								
					foreach($this->jointables AS $jointable){
				
						if(is_array($jointable['includefields']) && count($jointable['includefields']) > 0){
					
							foreach($jointable['includefields'] AS $val){
										
								$thisref[$val] = $row[$val];
							
							}
						
						}
						
					}
					
				}
				
				
			}else if(is_array($this->includefields)){
				
				foreach($this->includefields as $val){
					
					$thisref[$val] = $row[$val];
					
				}	
				
			}else{
				
				foreach($this->tablefields as $val){
					$thisref[$val] = $row[$val];
				}
			}

			$siblings[] = $thisref;
			
		}			
		
		return $siblings;
		
	}//////////////
	
	
	
	/**
	* A more efficient way to get data from child sets
	*/
	private function getChildNodesArray($parent, $level=0) {
		
		global $originalparentid;
		
		if($this->initparentid == null){
			
			$this->initparentid = "$parent"; // make it a string so it isn't null 0
			
		}
		
				
		$sql = $this->build_select_query() . $this->build_from_query();
		
		$sql .=  " WHERE {$this->dbtablename}.{$this->parent_id_field} = '$parent' ";
		
		if($this->sql_conditional != "") $sql .=  " AND ("  . $this->sql_conditional . ") ";
				
		$sql .=  " ORDER BY {$this->dbtablename}.{$this->position_field}";
		
		if($this->debug) echo "\n" . $sql . "\n";
		
	
		
		$result = mysql_query($sql);
			
		if($result){

		while ($row = mysql_fetch_array($result)) {
			
			$thisref = &$this->refs[ $row[$this->id_field] ];
			$thisref[$this->parent_id_field] = $row[$this->parent_id_field];
			$thisref[$this->position_field] = $row[$this->position_field];

			if(is_array($this->join_includefields)){
				
				foreach($this->join_includefields as $val){
					
					$thisref[$val] = $row[$val];
					
				}
				
				// phasing in for version 3 - Aug 31, 2012
				if(is_array($this->jointables) && count($this->jointables) > 0 ){
			
					
					foreach($this->jointables AS $jointable){
				
						if(is_array($jointable['includefields']) && count($jointable['includefields']) > 0){
					
							foreach($jointable['includefields'] AS $val){
										
								$thisref[$val] = $row[$val];
							
							}
						
						}
						
					}
					
				}
			
						
				
				
			}else if(is_array($this->includefields)){
				
				foreach($this->includefields as $val){
					
					$thisref[$val] = $row[$val];
					
				}	
				
			}else{
				
				foreach($this->tablefields as $val){
					
					$thisref[$val] = $row[$val];
					
				}		
				
				
			}
			
			
			$this->getChildNodesArray($row[$this->id_field], $level+1);
			
			if ($this->initparentid == $row[$this->parent_id_field]) { // is this a root node relative to the parent
				$this->list[ $row[$this->id_field] ] = &$thisref;								
			} else {
				$this->refs[ $row[$this->parent_id_field] ]['children'][ $row[$this->id_field] ] = &$thisref;
			}
			
		}
		
		
		
		}
		

		return $this->list;
		
		
	}////////// 

	

	
	/**
	* Get all the data from a single node
	*/
	public function getNode($id){
		
		$sql = $this->build_select_query() . $this->build_from_query();

		$sql .= " WHERE {$this->dbtablename}.{$this->id_field}='{$id}'";
		
		return mysql_fetch_assoc(mysql_query($sql));
		
		
	}////////////////

	/**
	* make a simple one dimensional array if ids and filed names
	*/
	public function getNodeIds($items){
		
		global $nodeids;
		

		if (count($items) && is_array($items)) {
			
			
			foreach ($items as $node_id=>$catvals) {
				
				$nodeids[] = $node_id;

				
				if (isset($catvals['children']) && count($catvals['children'])) {
					
					$this->getNodeIds($catvals['children']);
					
				}
								
			}
			

		}
		
		return $nodeids;
		
	}////////////////
	


	
	/**
	*  Move a node to a new parent. With Adjacency, this single record update will move all child nodes as well
	*/
	public function reparentNode($id,$new_parent_id){
		
		if($id != $new_parent_id){
			$sql = "UPDATE {$this->dbtablename} SET {$this->parent_id_field}='{$new_parent_id}' WHERE {$this->id_field}='{$id}'";
			mysql_query($sql);
		}
		
		
		
	}//////////////


	
	/**


	* Update a node
	*@return null
	*/	
	public function updateNode($id,$data){
		
		$fnames = array();

		foreach($data as $key=>$val){
			
			if($key != $this->id_field) $fnames[] = " $key = '$val' ";
			

		}
		

		$sql = "UPDATE {$this->dbtablename} SET ";
		
		$sql .= implode(",",$fnames);
		
		$sql .= " WHERE {$this->id_field} = '{$id}'";
		
		
		mysql_query($sql);

		
		
		

	}////////////////
	

	/**
	* Add a node
	*@return id of new node
	*/
	public function addNode($parent_id,$data){
		
		$fnames = array();
		$fvals = array();
		
		foreach($data as $key=>$val){
			$fnames[] = $key;
			$fvals[] = $val;	
		}
		
		$sql = "INSERT INTO " . $this->dbtablename;

		$sql .= " (" . $this->parent_id_field . ",";
		
		$sql .= implode(",",$fnames);
		
		$sql .= ") VALUES ('".$parent_id."','";
		
		$sql .= implode("','",$fvals);
		
		$sql .= "')";
		
		mysql_query($sql);
		
		
		return mysql_insert_id($this->dbConnectionID);
		
		
	}////////////////
	

	
	/**
	* Add a node next to a sibling
	*@params $sibling_id, $data
	*@return id of new node
	*/
	public function addSiblingNode($sibling_id,$data){
		
		$parent_id = $this->getParentID($sibling_id);
		
		return $this->addNode($parent_id,$data);

		
	}////////////////
	
	
	/**
	* Get all ids for child notes that are to be be deleted
	*@return array
	*/
	public function getIndentedNodeIds($items) {
		
		global $group_indent, $indentedarray;
		
		
		if(!is_array($indentedarray)) $indentedarray = array();
		
		if (count($items) && is_array($items)) {
			
			$group_indent ++;
			
			foreach ($items as $node_id=>$catvals) {
				
				
				$indentedarray[$node_id] = $catvals;
				
				$indentedarray[$node_id]['indent'] = $group_indent;
				

				if (count($catvals['children'])) {
					
					$this->getIndentedNodeIds($catvals['children']);
					
				}
				
				
			}
			
			$group_indent --;
			
			
		}
		
		
		return $indentedarray;
		
	}////////////////////
	

	/**
	* Get all ids for child notes that are to be be deleted
	*@return array
	*/
	private function getdeleteids($items,$subloop = false){
		
		
		if($subloop == false){

			$this->deleteids = array();
		}

		if (count($items) && is_array($items)) {
			
			
			foreach ($items as $node_id=>$catvals) {
				
				$this->deleteids[] = $node_id;
				
				if (count($catvals['children'])) {
					
					$this->getdeleteids($catvals['children'],true); 

				}
				
			}
		}
		
		return $this->deleteids;

	}//////////////////


	


	/**
	* DELETE CATEGORY AND all child nodes
	*/
	public function deleteNode($id){
		

		$deleteitems = $this->getChildNodes($id);
		
		$deleteids = $this->getdeleteids($deleteitems);

		$deleteids[] = $id;
		
		$sql = "DELETE FROM {$this->dbtablename} WHERE {$this->dbtablename}.{$this->id_field}='";
		
		$sql .= implode("' OR " . $this->dbtablename.".".$this->id_field . "='",$deleteids);
		
		$sql .= "';";
		
		mysql_query($sql);
		
		
		
		if(is_array($this->join_includefields) && $this->deletejoindata){
			
			$sql = "DELETE FROM {$this->jointable} WHERE {$this->jointable}.{$this->joinkey}='";
			
			$sql .= implode("' OR " . $this->jointable.".".$this->joinkey . "='",$deleteids);
			
			$sql .= "';";
			
			mysql_query($sql);
			
		}	
		

		
	}//////////////
	
	
	/**
	* create the from statements that include joined tables
	*
	*/
	private function build_from_query(){
	
	
		$sql = " FROM {$this->dbtablename}"; 
		
		
		if(is_array($this->join_includefields)){
			$sql .= " JOIN {$this->jointable} ON  {$this->dbtablename}.{$this->id_field} = {$this->jointable}.{$this->joinkey}";
		}
		
		// phasing in for version 3 - Aug 31, 2012
		if(is_array($this->jointables) && count($this->jointables) > 0 ){
		
			foreach($this->jointables AS $jointable){
						
				$sql .= " LEFT JOIN {$jointable['table']} ON  {$this->dbtablename}.{$this->id_field} = {$jointable['table']}.{$jointable['key']}";
			}
		}
		
		
		
		return $sql;
		
		
	
	}
	
	/**
	* Build a select query to include all joined tables
	*
	*/
	private function build_select_query(){
	
	
		$sql = "SELECT ";
				
		if(is_array($this->join_includefields)){
			
			$joinselectarray = array();
			
			foreach($this->join_includefields as $val){
				
				$joinselectarray[] = "{$this->jointable}.{$val}";
				
			}
			
			
			// phasing in for version 3 - Aug 31, 2012
			if(is_array($this->jointables) && count($this->jointables) > 0 ){
			
					
				foreach($this->jointables AS $jointable){
				
					if(is_array($jointable['includefields']) && count($jointable['includefields']) > 0){
					
						foreach($jointable['includefields'] AS $val){

							$joinselectarray[] = "{$jointable['table']}.{$val}";
							
						}
						
					}
						
				}
					
			}
			
			$sql .= implode(", ",array_merge( array($this->dbtablename.".".$this->id_field, $this->dbtablename.".".$this->parent_id_field,$this->dbtablename.".".$this->position_field),$joinselectarray));

	
			
		}else if(is_array($this->includefields)){
			$sql .= implode(", ",array_merge( array($this->dbtablename.".".$this->id_field, $this->dbtablename.".".$this->parent_id_field,$this->dbtablename.".".$this->position_field),$this->includefields));
			
		}else{
			
			$this->setFieldNames();
			$sql .= " * ";
			
		}
		
		return $sql;
		
	
	}
	
}



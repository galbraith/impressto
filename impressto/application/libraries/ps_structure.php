<?
/*
 *
 * -------------------------------------------------------------
 * Author:	 Gordon Montgomery
 * Date:	 July 2009
 * File:     class.structure.php
 * Type:     class
 * Name:     class_ps_structure
 * Purpose:  Converts data from a database into a multi-dimensional array
 *			 that is similar to JSON which represents the structure of the website.
 *			 It also caches hierarchical data such as a pages family, neighbours, etc
 *			 which can be used so that pages 'know' where they are
 *			 and their relationship to all of the pages around them
 * -------------------------------------------------------------
 */
class ps_structure
{
	private $site_matrix = array();
	private $section_cache = array();
	private $load_status;
	private $rootlevel = -8;
	private $init_id = 0;
	private $lang;
	
	// Class Constructor. 	
	public function __construct(){

		

	}
	
	
	public function set_init_id($local_init_id){
	
		//sets the currently initialized page that this class will be based on
		$this->init_id = $local_init_id;

	}
	
	public function set_local_lang($local_lang){
	
		//sets the currently initialized page that this class will be based on
		$this->lang = $local_lang;

	}
	
	/*
	public function init(){
	
		// gets the most common data elements automatically
		if ($this->f_fetch_data_from_sql()) {
			$this->f_fetch_sections($this->init_id);
			$this->f_fetch_neighbours($this->init_id);
			$this->load_status = "data_loaded";
		} else {
			$this->load_status = "data_not_found";
		}

	}
	*/
	
	/*
	This function recieves a data request, checks to see whether it can return that request, and if 
	it can't find the request in var_cache, it requests f_populate to fetch it and populate var_cache. 
	f_populate either successfully fetches the data, or returns "data_not_found"
	*/
	public function get($command, $id=0) {
		$returnval = "";
		while ($returnval == "") {
			if ($command == "load_status") {
				$returnval = $this->load_status;
			// returns all of the data stored in site_matrix. This allows site_matrix data to remain read-only
			// to all code outside of the class, thus preventing errors or accidental unauthorized priviledges.
			} elseif ($command == "site_matrix") {
				$returnval = $this->site_matrix;
			//get a specific node's value manually if it already exist
			} elseif (isset($this->site_matrix[$id][$command])) {
				$returnval = $this->site_matrix[$id][$command];
			//get a specific dataset from the cache if it already exist
			} elseif (isset($this->section_cache[$command])) {
				$returnval = $this->section_cache[$command];
			//otherwise try to figure it out				
			} else {
				$returnval = "command_not_recognized"; //$this->f_delegate($command, $id);
			}
		}
		return $returnval;
	}
	/*
	This function returns all of the data stored in section_cache. This allows section_cache data to remain read-only
	to all code outside of the class, thus preventing errors or accidental unauthorized priviledges.
	*/
	public function get_ALL() {
		return $this->section_cache;
	}
	/*
	This function populates the section_cache with various sets of arbitrary hiearchical data
	*/
	private function f_fetch_neighbours($this_page_id){
		$this->section_cache["my_parent_id"] = $this->f_get_parent($this_page_id);
		$this->section_cache["my_prev_id"] = $this->f_get_prev_id($this_page_id);
		$this->section_cache["my_next_id"] = $this->f_get_next_id($this_page_id);
		$this->section_cache["parent_prev_id"] = $this->f_get_prev_id($this->f_get_parent($this_page_id));
		$this->section_cache["parent_next_id"] = $this->f_get_next_id($this->f_get_parent($this_page_id));
		$this->section_cache["my_sibling_list"] = $this->f_get_siblings($this_page_id);
		$this->section_cache["my_children_list"] = $this->f_get_children($this_page_id);
		$this->section_cache["my_lineage"] = $this->f_get_ancestry($this_page_id);
		
	}
	/*
	This function connects to a mysql database, retrieves site hierarchy data, converts it into a 2D array,
	and sets the site_matrix with data from the completed array.
	*/
	private function f_fetch_data_from_sql() {
		$success = false;
		$tablename = "ps_content".devLiveR("drafts","")."_".$this->lang;
		
		$sql = "SELECT CO_ID,CO_Parent,CO_Weight,CO_seoTitle,CO_Active";
		$sql .=" FROM ps_contentsections_".$this->lang;
		$sql .=" UNION ALL SELECT CO_ID,CO_Parent,CO_Weight,CO_seoTitle,CO_Active";
		$sql .=" FROM ".$tablename.devLiveR(""," WHERE CO_Active=1");
		$sql .=" ORDER BY CO_Parent,CO_Weight ASC";
		$result = mysql_query($sql) or die(mysql_error());	
		mysql_data_seek($result,0);//reset pointer
		$return_matrix = array();
		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			if ($row["CO_ID"]) {
				$row_array = array();
				$row_array["CO_ID"] = $row["CO_ID"];
				$row_array["CO_Parent"] = $row["CO_Parent"];
				$row_array["CO_seoTitle"] = $row["CO_seoTitle"];
				$row_array["CO_Weight"] = $row["CO_Weight"];
				$row_array["CO_Active"] = $row["CO_Active"];
				
				$this->site_matrix[$i] = $row_array;
				$i++;
				unset($row_array);
				$success = true;
			}
		}
		return $success;
	}
	/*
	This function populates the section_cache with the page sections nav elements first,
	and then continually populates it with all of the nav levels
	*/
	private function f_fetch_sections($this_page_id) {
		foreach($this->site_matrix as $key) {
			if ($key["CO_ID"] < 0) {
				$var = $this->f_get_children($key["CO_ID"]);
				if ($var != "data_not_found") {
					$name = str_replace(" ", "_", str_replace("-", "_", $key["CO_seoTitle"]));
					$this->section_cache[$name] = $var;
				}
			}
		}
		
		$i=0;
		while ($i != -1) {
			$var = $this->f_nav_level($this_page_id, $i);
			if ($var != "data_not_found") {
				$this->section_cache["nav_level_".($i+1)] = $var;
				$i++;
			} else {
				$i=-1;
			}
		}
	}
	/*
	This function searches for and returns the id of a given id's parent
	*/
	private function f_get_parent($p_var) {
		$return_id = "data_not_found";
		foreach($this->site_matrix as $key) { //for each node
			if ($key["CO_ID"] == $p_var) { //check to see if the node we are looking for is same as node in array
				$return_id = $key['CO_Parent']; //get that node's parent id
				return $return_id;
			}
		}
		return $return_id;
	}
	/*
	This function searches for and retrieves the id of a node that is it's previous sibling, which may
	include looping forward to the last sibling if it is the first. Otherwise, it returns data_not_found.
	*/
	private function f_get_prev_id($p_var) {
		$returnnav = "data_not_found";
		$i=0;
		$sib_navs = $this->f_get_siblings($p_var);
		if ($sib_navs != "data_not_found") {
			$last_index = count($sib_navs)-1;

			foreach($sib_navs as $key) {
				if($p_var == $key["CO_ID"]) {
					if ($i == 0) {
						$j = $last_index;
					} else {
						$j = $i-1;
					}
					$returnnav = $sib_navs[$j]["CO_ID"]; //previous node in group
				}
				$i++;
			}
		}
		return $returnnav;
	}
	/*
	This function searches for and retrieves the id of a node that is it's next sibling, which may
	include looping back to the first sibling if it is the last. Otherwise, it returns data_not_found.
	*/
	private function f_get_next_id($p_var) {
		$returnnav = "data_not_found";
		$i=0;
		$sib_navs = $this->f_get_siblings($p_var);
		if ($sib_navs != "data_not_found") {
			$last_index = count($sib_navs)-1;
	
			
			foreach($sib_navs as $key) {
				if($p_var == $key["CO_ID"]) {
					if ($i == $last_index) {
						$k = 0;
					} else {
						$k = $i+1;
					}
					$returnnav = $sib_navs[$k]["CO_ID"]; //next node in group
				}
				$i++;
			}
		}
		return $returnnav;
	}
	/*
	This function searches for all of the siblings of a given node
	and return an array with all of these sibling
	*/
	private function f_get_siblings($p_var) {
		$return_array = array();
		$i=0;
		$parent = $this->f_get_parent($p_var);
		
		foreach($this->site_matrix as $key) {		
			if ($key['CO_Parent'] == $parent) {
				$return_array[$i] = $key;
				$i++;
			}
		}
		if ($i == 0) {
			$return_array = "data_not_found";
		}
		return $return_array;
	}
	/*
	This function returns an array list of children id's of a particular node id
	*/
	private function f_get_children($p_var) {
		$return_array = array();
		$i=0;
		
		foreach($this->site_matrix as $key) {
			if ($key['CO_Parent'] == $p_var) {
				$return_array[$i] = $key;
				$i++;
			}
		}
		if ($i == 0) {
			$return_array = "data_not_found";
		}
		return $return_array;
	}
	/*
	This function recursively checks to see if a child node is descended from
	a particular parent node, or a sibling
	*/
	private function f_get_am_i_in_family($id, $ancestor) {
		$return_bool = false;
		$loop = true;
		
		$parent = $this->f_get_parent($id);
			
		if ($parent == $ancestor || $id == $ancestor) {
			$return_bool = true;
		}
		
		while ($loop) {
			if ($parent != $this->rootlevel) {
				if ($parent == $ancestor){
					$return_bool = true;
					$loop = false;
				} else {
					$parent = $this->f_get_parent($parent);
				}
			} else {
				$loop = false;
			}
		}
		return $return_bool;
	}
	/*
	This function is given the current page id, as well as a nav level, and then fetches that nav level's 
	list of page id's. The function can retrieve an unlimited number 
	*/
	private function f_nav_level($id, $nav_level) {
		$returnval = "data_not_found";
		
		$ancestry_array = $this->f_get_ancestry($id);

		if ( isset( $ancestry_array[$nav_level] ) ) {
			$returnval = $this->f_get_children($ancestry_array[$nav_level]);
		}
		
		return $returnval;	
	}
	/*
	This function returns an array with the list of a given node's most senior parent, second most senior parent,
	third, and so on until the last item of the array which is the given node itself.
	*/
	private function f_get_ancestry($parent) {
		$return_array = array();
		$return_array[0] = $parent;
		
		while($parent != $this->rootlevel) {
			$parent = $this->f_get_parent($parent);
			if (($parent < 0 && $parent != $this->rootlevel) || $parent=="data_not_found") {
				$parent = $this->rootlevel;
				$return_array[] = $parent;
			} else {
				$return_array[] = $parent;
			}
		}
		return array_reverse($return_array);	
	}
	/*
	This function searches the site_matrix for a particular field that contains a particular value, and then
	returns the index of the last found matching row. It returns data_not_found if there are no search results
	*/
	private function f_search_sitematrix($field, $value) {
		$returnval = "data_not_found";
		$i=0;
		
		foreach($this->site_matrix as $key) {
			if (isset($key[$field])) {
				if ($key[$field] == $value) {
					$returnval = $key["CO_ID"];
				}
			}
			$i++;
		}
		return $returnval;
	}	
}



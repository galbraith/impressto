<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Events
 *
 * A simple events system for CodeIgniter.
 *
 * @version		1.0
 * @author		Dan Horrigan <http://dhorrigan.com>
 * @author		Eric Barnes <http://ericlbarnes.com>
 * @license		Apache License v2.0
 * @copyright	2010 Dan Horrigan
 * @package		PyroCMS\Core\Libraries
 */

/**
 * Events Library
 */
class Events
{

	/**
	 * An array of listeners
	 * 
	 * @var	array
	 */
	protected static $_listeners = array();

	/**
	 * Constructor
	 * 
	 * Load up the modules. 
	 */
	public function __construct()
	{
		self::_load_modules();
	}

	/**
	 * Load Modules
	 *
	 * Loads all active modules
	 */
	private static function _load_modules()
	{
		$_ci = get_instance();

		$is_core = TRUE;

		$_ci->load->model('ps_modules');
		
	
		if (!$results = $_ci->ps_modules->get_all())
		{
			return FALSE;
		}
					

		foreach ($results as $module)
		{
			// This does not have a valid details.php file! :o
			if (!$spawned_class = self::_spawn_class($module))
			{
				continue;
			}else{
			
				
			
			}
		}
		

		return TRUE;
	}

	/**
	 * Spawn Module Events Class
	 *
	 * Checks to see if a events.php exists and returns a class
	 * 
	 * @param string $module The folder name of the module.
	 * @return object|boolean 
	 */
	private static function _spawn_class($module)
	{
	
		$CI = get_instance();
				
		$modules_locations = $CI->config->item('modules_locations');
			
		$events_file = "";
		
		foreach ($modules_locations as $ml_fullpath => $ml_rel) {	
				
			$testpath = "{$ml_fullpath}{$module}/events.php";
				
			if (is_file($testpath)) $events_file = $testpath;	
		}
		
		if($events_file == "") return FALSE;
		
		include_once $events_file;

		// Now call the details class
		$class = 'Events_'.ucfirst(strtolower($module));

		// Now we need to talk to it
		return class_exists($class) ? new $class : FALSE;
	
	}
	
	

	/**
	 * Register
	 *
	 * Registers a Callback for a given event
	 *
	 * @param string $event The name of the event.
	 * @param array $callback The callback for the event.
	 */
	public static function register($event, array $callback)
	{
		$key = get_class($callback[0]).'::'.$callback[1];
		self::$_listeners[$event][$key] = $callback;
	}

	/**
	 * Triggers an event and returns the results.
	 * 
	 * The results can be returned in the following formats:
	 *  - 'array'
	 *  - 'json'
	 *  - 'serialized'
	 *  - 'string'
	 *
	 * @param string $event The name of the event
	 * @param string $data Any data that is to be passed to the listener
	 * @param string $return_type The return type
	 * @connect_data bool $connect_data join data into a single variable
	 * @return string|array The return of the listeners, in the return type
	
	 */
	public static function trigger($event, $data = '', $return_type = 'string', $connect_data = FALSE)
	{
	
		$calls = array();

		if (self::has_listeners($event))
		{
		
			foreach (self::$_listeners[$event] as $listener)
			{
				if (is_callable($listener))
				{
				
					if($connect_data){
						$data = call_user_func($listener, $data);
						
					}else{
						$calls[] = call_user_func($listener, $data);
					}
				}
			}
		}
		
		if($connect_data) $calls[] = $data;
				
		
		return self::_format_return($calls, $return_type);
	}

	/**
	 * Format Return
	 *
	 * Formats the return in the given type
	 *
	 * @param array $calls The array of returns
	 * @param string $return_type The return type
	 * @return array|null The formatted return
	 */
	protected static function _format_return(array $calls, $return_type)
	{
		//log_message('debug', 'Events::_format_return() - Formating calls in type "'.$return_type.'"');

		switch ($return_type)
		{
			case 'array':
				return $calls;
				break;
			case 'json':
				return json_encode($calls);
				break;
			case 'serialized':
				return serialize($calls);
				break;
			case 'string':
				$str = '';
				foreach ($calls as $call)
				{
					$str .= $call;
				}
				return $str;
				break;
			default:
				return $calls;
				break;
		}

		// Does not do anything, so send NULL. FALSE would suggest an error
		return NULL;
	}

	/**
	 *
	 * @access	public
	 * @param	string	
	 * @return	bool	
	 */

	/**
	 * Checks if the event has listeners
	 *
	 * @param string $event The name of the event
	 * @return boolean Whether the event has listeners
	 */
	public static function has_listeners($event)
	{
		log_message('debug', 'Events::has_listeners() - Checking if event "'.$event.'" has listeners.');

		if (isset(self::$_listeners[$event]) AND count(self::$_listeners[$event]) > 0)
		{
			return TRUE;
		}

		return FALSE;
	}

}
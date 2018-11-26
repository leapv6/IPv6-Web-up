<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log {

	/**
	 * Path to save log files
	 *
	 * @var string
	 */
	protected $_log_path;

	/**
	 * Level of logging
	 *
	 * @var int
	 */
	protected $_threshold = 1;

	/**
	 * Array of threshold levels to log
	 *
	 * @var array
	 */
	protected $_threshold_array = array();

	/**
	 * Format of timestamp for log files
	 *
	 * @var string
	 */
	protected $_date_fmt = 'Y-m-d H:i:s';

	/**
	 * Filename extension
	 *
	 * @var	string
	 */
	protected $_file_ext;

	/**
	 * Whether or not the logger can write to the log files
	 *
	 * @var bool
	 */
	protected $_enabled = TRUE;

	/**
	 * Predefined logging levels
	 *
	 * @var array
	 */
	protected $_levels = array('ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4);

	/**
	 * mbstring.func_overload flag
	 *
	 * @var	bool
	 */
	protected static $func_overload;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$config =& get_config();

		isset(self::$func_overload) OR self::$func_overload = (extension_loaded('mbstring') && ini_get('mbstring.func_overload'));

		$this->_log_path = ($config['log_path'] !== '')
			? rtrim($config['log_path'], '/\\').DIRECTORY_SEPARATOR : APPPATH.'logs'.DIRECTORY_SEPARATOR;

		$this->_file_ext = (isset($config['log_file_extension']) && $config['log_file_extension'] !== '')
			? ltrim($config['log_file_extension'], '.') : 'log';

		file_exists($this->_log_path) OR mkdir($this->_log_path, 0755, TRUE);

		if ( ! is_dir($this->_log_path) OR ! is_really_writable($this->_log_path))
		{
			$this->_enabled = FALSE;
		}

		if (is_numeric($config['log_threshold']))
		{
			$this->_threshold = (int) $config['log_threshold'];
		}
		elseif (is_array($config['log_threshold']))
		{
			$this->_threshold = 0;
			$this->_threshold_array = array_flip($config['log_threshold']);
		}

		if ( ! empty($config['log_date_format']))
		{
			$this->_date_fmt = $config['log_date_format'];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @param	string	$level 	The error level: 'error', 'debug' or 'info'
	 * @param	string	$msg 	The error message
	 * @return	bool
	 */
	public function write_log($level, $msg)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if (( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
			&& ! isset($this->_threshold_array[$this->_levels[$level]]))
		{
			return FALSE;
		}

		$filepath = $this->_log_path.'log-'.($level=='ERROR'?'err':'').date('Y-m-d').'.'.$this->_file_ext;

		// Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
		if (strpos($this->_date_fmt, 'u') !== FALSE)
		{
			$microtime_full = microtime(TRUE);
			$microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
			$date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
			$date = $date->format($this->_date_fmt);
		}
		else
		{
			$date = date($this->_date_fmt);
		}

		return error_log($level.' - '.$date.' --> '.$msg."\n",3,$filepath);
	}
}

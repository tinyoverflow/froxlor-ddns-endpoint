<?php

/** Prevent direct access to this file */
if (defined('APPLICATION') === false) {
	die('Direct access not permitted!');
}

class Config
{
	/**
	 * Name of the file this class represents.
	 * @var string
	 */
	private $filename = '';

	/**
	 * Entire configuration array this class represents.
	 * @var array
	 */
	private $config = [];

	/**
	 * Creates a new instance of this class.
	 * @param string $filename Path of the file to use.
	 * @return void
	 */
	public function __construct(string $filename)
	{
		$this->filename = $filename;
		$this->config = $this->readConfig($filename);
	}

	/**
	 * Returns a specific item from the array by dot notation.
	 * @param string $path Dot notated path to the array item.
	 * @param mixed $default Default value if the result is null.
	 * @return mixed The value of that item in the array.
	 */
	public function get($path, $default = null)
	{
		$array = $this->config;
		$parts = explode('.', $path);

		foreach ($parts as $part) {
			if (isset($array[$part]) === false) {
				return $default;
			}

			$array = $array[$part];
		}

		return $array ?? $default;
	}

	/**
	 * Returns the content of the specified file.
	 * @param string $filename Path of the file to load.
	 * @return mixed File contents.
	 */
	private function readConfig(string $filename)
	{
		return include $filename;
	}
}

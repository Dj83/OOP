<?php

// Set a flag to the Platform that this is the entry point.
define('_JEXEC', 1);

// Setup the base path related constant.
define('JPATH_BASE', dirname(__FILE__));
define('JPATH_SITE', JPATH_BASE);
define('JPATH_PAYPAL', JPATH_SITE);
define('JPATH_THEMES', JPATH_BASE.'/themes');
define('JPATH_APPS', JPATH_BASE.'/checkout');

// Bootstrap the application.
require dirname(__FILE__).'/bootstrap.php';

// Set error handler to echo
JError::setErrorHandling(E_ERROR, 'echo');

class PayPal extends JApplicationWeb
{
	public static $router = null;
	public function __construct(JInput $input = null, JRegistry $config = null, JWebClient $client = null)
	{
/*		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData());

		// Set the execution datetime and timestamp;
		$this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
		$this->set('execution.timestamp', time());

		// Setup the response object.
		$this->response = new stdClass;
		$this->response->cachable = false;
		$this->response->headers = array();
		$this->response->body = array();

		// Set the system URIs.
		$this->loadSystemUris();
*/

		parent::__construct($input,$config,$client);
	}

	public function initialize($session = null, $document = null, $language = null, $dispatcher = null)
	{
		parent::initialise($session,$document,$language,$dispatcher);
		JFactory::$session =& $this->session;
		JFactory::$document =& $this->document;
		JFactory::$application =& $this;
	}
	/**
	 * Method to run the Web application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of action that populates a JDocument object so that output
	 * can be rendered to the client.
	 *
	 * @return  void
	 * @since   12.1
	 */
	protected function doExecute()
	{
		ob_start();
		require_once(dirname(__FILE__).'/apps/checkout/checkout.php');
		$app = ob_get_clean();
		
		// Push the output into the document buffer.
		$this->document->setBuffer($app, array('type' => 'app', 'name' => 'main'));
	}
	/**
	 * Checks the accept encoding of the browser and compresses the data before
	 * sending it to the client if possible.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	protected function compress()
	{
		// Supported compression encodings.
		$supported = array(
			'x-gzip' => 'gz',
			'gzip' => 'gz',
			'deflate' => 'deflate'
		);

		// Get the supported encoding.
		$encodings = array_intersect($this->client->encodings, array_keys($supported));

		// If no supported encoding is detected do nothing and return.
		if (empty($encodings))
		{
			return;
		}

		// Verify that headers have not yet been sent, and that our connection is still alive.
		if ($this->checkHeadersSent() || !$this->checkConnectionAlive())
		{
			return;
		}

		// Iterate through the encodings and attempt to compress the data using any found supported encodings.
		foreach ($encodings as $encoding)
		{
			if (($supported[$encoding] == 'gz') || ($supported[$encoding] == 'deflate'))
			{
				// Verify that the server supports gzip compression before we attempt to gzip encode the data.

				// @codeCoverageIgnoreStart
				if (!extension_loaded('zlib') || ini_get('zlib.output_compression'))
				{
					continue;
				}
				// @codeCoverageIgnoreEnd

				// Attempt to gzip encode the data with an optimal level 4.
				$data = $this->getBody();
				$gzdata = gzencode($data, 4, ($supported[$encoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

				// If there was a problem encoding the data just try the next encoding scheme.
				// @codeCoverageIgnoreStart
				if ($gzdata === false)
				{
					continue;
				}
				// @codeCoverageIgnoreEnd

				// Set the encoding headers.
				$this->setHeader('Content-Encoding', $encoding);
				$this->setHeader('X-Content-Encoded-By', 'Mercury CRM');

				// Replace the output with the encoded data.
				$this->setBody($gzdata);

				// Compression complete, let's break out of the loop.
				break;
			}
		}
	}
	public function getTemplate()
	{
		return $this->get('theme');
	}
	public function getMenu()
	{
		return null;
	}
	public static function getRouter()
	{
		return null;
		if(!self::$router){
			jimport('joomla.application.router');
			self::$router = JRouter::getInstance('PayPal');
		}
		return self::$router;
	}
}
JApplicationHelper::addClientInfo((object) array('id'=>3,'name'=>'PayPal','path'=>JPATH_PAYPAL));
$app = JApplicationWeb::getInstance('PayPal');
$app->initialize();
$app->execute();
//echo $app;

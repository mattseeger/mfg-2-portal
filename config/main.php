<?php
error_reporting(E_ALL);
/**
 * CONATINS ALL GLOBALLY USABLE INFORMATION
 */

/**
 * 
 * This will be / if the mvc is installed at the document root. 
 */
//define('BASE_URL', '/');
define('BASE_URL', '/');

/**
 * 
 * The name of the default controller class
 * This will be used when no cotroller is given and when the requested
 * controller does not exist
 */
//define('DEFAULT_CONTROLLER', "Index");
define('DEFAULT_CONTROLLER', 'Index');

/**
 * 
 * The name of the default method to call when no method is given and when
 * the requested method does not exist
 */
//define('DEFAULT_METHOD', 'Index');
define('DEFAULT_METHOD', 'Index');

/**
 * 
 * SFDC Username for Integration
 */
define('SFDC_API_USER', 'api@mfg.qtc.demo');

/**
 * SFDC Password for Integration
 */
define('SFDC_API_PASSWORD', '562471cbcb0dd0fa42a76665acf0dbb68479b78a');

/**
 * SFDC API Token for Integration
 */
define('SFDC_API_TOKEN', '');

/**
 * SETTING THE LOCALE
 * @todo make this multi currency based on the Browser locale if needed
 */
setlocale(LC_MONETARY, 'en_US');








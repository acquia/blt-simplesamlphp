<?php

/**
 * @file
 * SimpleSamlPhp Acquia Configuration.
 *
 * This file was last modified on in July 2018.
 *
 * All custom changes below. Modify as needed.
 */

/**
 * Defines Acquia account specific options in $config keys.
 *
 *   - 'store.sql.name': Defines the Acquia Cloud database name which
 *     will store SAML session information.
 *   - 'store.type: Define the session storage service to use in each
 *     Acquia environment ("defualts to sql").
 */

// Set some security and other configs that are set above, however we
// overwrite them here to keep all changes in one area.
$config['technicalcontact_name'] = "Your Name";
$config['technicalcontact_email'] = "your_email@yourdomain.com";

// Change these for your installation.
$config['secretsalt'] = 'mysecretsalt';
$config['auth.adminpassword'] = 'mysupersecret';

/**
 * Support SSL Redirects to SAML login pages.
 *
 * Uncomment the following code block to set server port to 443 on HTTPS
 * environment.
 *
 * This is a requirement in SimpleSAML when providing a redirect path.
 *
 * @link https://github.com/simplesamlphp/simplesamlphp/issues/450
 *
 * @code
 * $_SERVER['SERVER_PORT'] = 443;
 * $_SERVER['HTTPS'] = 'true';
 * $protocol = 'https://';
 * $port = ':' . $_SERVER['SERVER_PORT'];
 * @endcode
 */

/**
 * Cookies No Cache.
 *
 * Allow users to be automatically logged in if they signed in via the same
 * SAML provider on another site by uncommenting the setcookie line below.
 *
 * Warning: This has performance implications for anonymous users.
 *
 * @link https://docs.acquia.com/resource/using-simplesamlphp-acquia-cloud-site
 *
 * @code
 * setcookie('NO_CACHE', '1');
 * @endcode
 */

/**
 * Generate Acquia session storage via hosting creds.json.
 *
 * Session sorage defaults using the database for the current request.
 *
 * @link https://docs.acquia.com/resource/using-simplesamlphp-acquia-cloud-site/#storing-session-information-using-the-acquia-cloud-sql-database
 */

if (!getenv('AH_SITE_ENVIRONMENT')) {
  // Add / modify your local configuration here.
  $config['store.type'] = 'sql';
  $config['store.sql.dsn'] = sprintf('mysql:host=%s;port=%s;dbname=%s', '127.0.0.1', '', 'drupal');
  $config['store.sql.username'] = 'drupal';
  $config['store.sql.password'] = 'drupal';
  $config['store.sql.prefix'] = 'simplesaml';
  $config['certdir'] = "/var/www/simplesamlphp/cert/";
  $config['metadatadir'] = "/var/www/simplesamlphp/metadata";
  $config['baseurlpath'] = 'simplesaml/';
  $config['loggingdir'] = '/var/www/simplesamlphp/log/';

}
elseif (getenv('AH_SITE_ENVIRONMENT')) {
  // Support multi-site and single site installations at different base URLs.
  // Overide $config['baseurlpath'] = "https://{yourdomain}/simplesaml/"
  // to customize the default Acquia configuration.
  // phpcs:ignore
  $config['baseurlpath'] = $protocol . $_SERVER['HTTP_HOST'] . $port . '/simplesaml/';
  // Set ACE and ACSF sites based on hosting database and site name.
  $ah_site_dir = getenv('AH_SITE_GROUP') . '.' . getenv('AH_SITE_ENVIRONMENT');
  $config['certdir'] = '/mnt/www/html/' . $ah_site_dir . '/simplesamlphp/cert/';
  $config['metadatadir'] = '/mnt/www/html/' . $ah_site_dir . '/simplesamlphp/metadata';
  $config['baseurlpath'] = 'simplesaml/';
  // Setup basic file based logging.
  $config['logging.handler'] = 'file';
  // On Acquia Cloud Next, the preferred location is /shared/logs
  // on Acquia Cloud Classic, the preferred location is the same directory as
  // ACQUIA_HOSTING_DRUPAL_LOG.
  $config['loggingdir'] = (file_exists('/shared/logs/')) ? '/shared/logs/' : dirname(getenv('ACQUIA_HOSTING_DRUPAL_LOG'));
  $config['logging.logfile'] = 'simplesamlphp-' . date('Ymd') . '.log';
  $creds_json = file_get_contents('/var/www/site-php/' . $ah_site_dir . '/creds.json');
  $creds = json_decode($creds_json, TRUE);
  $database = $creds['databases'][$_ENV['AH_SITE_GROUP']];
  // On Acquia Cloud Classic, the current active database host is determined
  // by a DNS lookup.
  if (isset($database['db_cluster_id'])) {
    require_once "/usr/share/php/Net/DNS2_wrapper.php";
    try {
      $resolver = new Net_DNS2_Resolver([
        'nameservers' => [
          '127.0.0.1',
          'dns-master',
        ],
      ]);
      $response = $resolver->query("cluster-{$database['db_cluster_id']}.mysql", 'CNAME');
      $database['host'] = $response->answer[0]->cname;
    }
    catch (Net_DNS2_Exception $e) {
      Logger::warning('DNS entry not found');
    }
  }
  $config['store.type'] = 'sql';
  $config['store.sql.dsn'] = sprintf('mysql:host=%s;port=%s;dbname=%s', $database['host'], $database['port'], $database['name']);
  $config['store.sql.username'] = $database['user'];
  $config['store.sql.password'] = $database['pass'];
  $config['store.sql.prefix'] = 'simplesaml';
}

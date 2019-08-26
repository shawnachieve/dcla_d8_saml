<?php
/**
 * @file
 * Provides platform.sh specific configuration settings.
 */

$config['technicalcontact_name'] = 'Shawn S. Smiley';
$config['technicalcontact_email'] = 'shawn.smiley@achieveinternet.com';
$config['timezone'] = 'America/Los_Angeles';
$config['auth.adminpassword'] = $_ENV['SAMLADMINPW'];
$config['enable.saml20-idp'] = TRUE;

// Set SimpleSAML to log using error_log(), which on Platform.sh will
// be mapped to the /var/log/app.log file.
$config['logging.handler'] = 'errorlog';

// Set SimpleSAML to use the metadata directory in Git, rather than
// the empty one in the vendor directory.
$config['metadata.sources'] = [
  ['type' => 'flatfile', 'directory' =>  dirname(__DIR__) . '/metadata'],
];

// Tell SimpleSAML where to find its encryption/TLS certificates.
$config['certdir'] = dirname(__DIR__) . '/cert';

// Setup the database connection for all parts of SimpleSAML.
if (isset($_ENV['PLATFORM_RELATIONSHIPS'])) {
  $relationships = json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), TRUE);
  foreach ($relationships['database'] as $instance) {
    if (!empty($instance['query']['is_master'])) {
      $dsn = sprintf("%s:host=%s;dbname=%s",
        $instance['scheme'],
        $instance['host'],
        $instance['path']
      );
      $config['database.dsn'] = $dsn;
      $config['database.username'] = $instance['username'];
      $config['database.password'] = $instance['password'];

      $config['store.type'] = 'sql';
      $config['store.sql.dsn'] = $dsn;
      $config['store.sql.username'] = $instance['username'];
      $config['store.sql.password'] = $instance['password'];
      $config['store.sql.prefix'] = 'simplesaml';

    }
  }
}

// Set the salt value from the Platform.sh entropy value, provided for this purpose.
// If not using platform.sh, you can generate this salt using:
// LC_CTYPE=C tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
if (isset($_ENV['PLATFORM_PROJECT_ENTROPY'])) {
  $config['secretsalt'] = $_ENV['PLATFORM_PROJECT_ENTROPY'];
}

// Security Config:
$config['admin.protectindexpage'] = TRUE;
$config['admin.protectmetadata'] = TRUE;
$config['trusted.url.domains'] = ['test-t6dnbai-2jlngt4rcela6.us-2.platformsh.site', 'www.test-t6dnbai-2jlngt4rcela6.us-2.platformsh.site'];
$config['showerrors'] = FALSE;
$config['errorreporting'] = FALSE;
$config['session.cookie.domain'] = '.test-t6dnbai-2jlngt4rcela6.us-2.platformsh.site';
$config['session.cookie.secure'] = TRUE;
$config['session.cookie.samesite'] = 'Lax';

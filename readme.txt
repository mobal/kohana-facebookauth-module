FacebookAuth module for Kohana 3.2. Based on 'facebook-php-sdk-v3.1.1-25-g6c82b3f'.
Modified 'facebook.php', now using Kohana's Session manager.

1. Create a folder 'facebookauth' in the modules directory.

2. Copy the files.

3. Edit 'config/facebook.php' file.

4. Edit the 'bootstrap.php' file, enable this module.

Example:

$fb = FacebookAuth::factory();

if($fb->logged_in())
{
	echo $fb->get('email');
}
else
{
	Request::current()->redirect($fb->login_url());
}

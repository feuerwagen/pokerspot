<?php
/***
 * Data Filtering Using PHP's Filter Functions - Part one
 * Examples using PHP's Filter Functions
 * http://devolio.com/blog/archives/413-Data-Filtering-Using-PHPs-Filter-Functions-Part-one.html
 **/
error_reporting(E_ALL);

/* do a quick check to make sure that the filter list is available */
if (function_exists('filter_list'))
{
	/* filter list found */
} else {
	die("Error: Filters not found.");
}


/* variables to test against */
$int = 432;
$bool = true;
$float = 432.43;
$reg = "/^([a-zA-Z0-9 ]){4,16}$/";
$url = "http://devolio.com/blog";
$email = 'joey@devolio.com';
$ipaddr = '127.0.0.1';
$ipres = "192.168.0.*";
$ipv6addr = "2001:0db8:85a3:08d3:1319:8a2e:0370:7334";
$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWX
YZ1234	567890`~!@#$%^&*()-_=+[{]};:'\"<,>.?/\|\\n\\r\\t";
$int_octal = decoct(800.82);
$int_hex = dechex(800.82);


/* grab all of the filters and show them */
echo "<h2>Filter list</h2><pre>";
echo "<ul>\n";
$filters = filter_list();
foreach ($filters as $filter)
{
	echo "<li>".$filter."</li>\n";
}
echo "</ul></pre>\n";


echo "<h2>FILTER_VALIDATE_*</h2>";


/* check if an integer is valid */
$valid_int = filter_var($int, FILTER_VALIDATE_INT);
echo "<pre><b>FILTER_VALIDATE_INT</b><br />";
if ($valid_int !== false)
{
	echo "Valid integer.</pre>";
} else {
	echo "Not a valid integer.</pre>";
}


/* check if a boolean is valid */
$valid_bool = filter_var($bool, FILTER_VALIDATE_BOOLEAN);
echo "<pre><b>FILTER_VALIDATE_BOOL</b><br />";
if ($valid_bool !== false)
{
	echo "Valid boolean.</pre>";
} else {
	echo "Not a valid boolean.</pre>";
}


/* check if a float (int) is valid */
$valid_float = filter_var($float, FILTER_VALIDATE_FLOAT);
echo "<pre><b>FILTER_VALIDATE_FLOAT</b><br />";
if ($valid_float !== false)
{
	echo "Valid float.</pre>";
} else {
	echo "Not a valid float.</pre>";
}


/* check if a regular expression is valid 
 * suppressed (bug?) in case regex not available
 */
$valid_reg = @filter_var($reg, FILTER_VALIDATE_REGEXP);
echo "<pre><b>FILTER_VALIDATE_REGEXP</b><br />";
if ($valid_reg !== false)
{
	echo "Valid regular expression.</pre>";
} else {
	echo "Not a valid regular expression.</pre>";
}


/* check if a URL is valid */
$valid_url = filter_var($url, FILTER_VALIDATE_URL);
echo "<pre><b>FILTER_VALIDATE_URL</b><br />";
if ($valid_url !== false)
{
	echo "Valid URL.</pre>";
} else {
	echo "Not a valid URL.</pre>";
}


/* check if an e-mail address is valid */
$valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
echo "<pre><b>FILTER_VALIDATE_EMAIL</b><br />";
if ($valid_email !== false)
{
	echo "Valid e-mail address.</pre>";
} else {
	echo "Not a valid e-mail address.</pre>";
}


/* check if an IP address is valid */
$valid_ip = filter_var($ipaddr, FILTER_VALIDATE_IP);
echo "<pre><b>FILTER_VALIDATE_IP</b><br />";
if ($valid_ip !== false)
{
	echo "Valid IP address.</pre>";
} else {
	echo "Not a valid IP address.</pre>";
}


echo "<h2>FILTER_SANITIZE_*</h2>";


/* sanitize filters */
/* check if filter unsafe raw is unsafe. protip: YES */
$raw = $string;
$valid_raw = filter_var($raw, FILTER_UNSAFE_RAW);
echo "<pre><b>FILTER_UNSAFE_RAW</b><br />".$valid_raw."</pre>";


/* sanitize string */
$san_string = filter_var($string, FILTER_SANITIZE_STRING);
echo "<pre><b>FILTER_SANITIZE_STRING</b><br />".$san_string."</pre>";


/* sanitize stripped */
$san_stripped = filter_var($string, FILTER_SANITIZE_STRIPPED);
echo "<pre><b>FILTER_SANITIZE_STRIPPED</b><br />".$san_stripped."</pre>";


/* sanitize encoded */
$san_enc = filter_var($string, FILTER_SANITIZE_ENCODED);
echo "<pre><b>FILTER_SANITIZE_ENCODED</b><br />".$san_enc."</pre>";


/* sanitize special chars */
$san_spc = filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
echo "<pre><b>FILTER_SANITIZE_SPECIAL_CHARS</b><br />".$san_spc."</pre>";


/* sanitize email */
$san_email = filter_var($string, FILTER_SANITIZE_EMAIL);
echo "<pre><b>FILTER_SANITIZE_EMAIL</b><br />".$san_email."</pre>";


/* sanitize url */
$san_url = filter_var($string, FILTER_SANITIZE_URL);
echo "<pre><b>FILTER_SANITIZE_URL</b><br />".$san_url."</pre>";


/* sanitize int */
$san_int = filter_var($string, FILTER_SANITIZE_NUMBER_INT);
echo "<pre><b>FILTER_SANITIZE_NUMBER_INT</b><br />".$san_int."</pre>";


/* sanitize float */
$san_float = filter_var($string, FILTER_SANITIZE_NUMBER_FLOAT);
echo "<pre><b>FILTER_SANITIZE_NUMBER_FLOAT</b><br />".$san_float."</pre>";


/* sanitize magic quotes */
$san_mquotes = filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES);
echo "<pre><b>FILTER_SANITIZE_MAGIC_QUOTES</b><br />".$san_mquotes."</pre>";


echo "<h2>FILTER_FLAG_*</h2>";


/* filter flags */
/* allow octal (for int filters) */
$allow_octal = filter_var($int_octal, FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_ALLOW_OCTAL);
echo "<pre><b>FILTER_FLAG_ALLOW_OCTAL (int filters only)</b><br />".$allow_octal."</pre>";


/* allow hex (for int filters) */
$allow_hex = filter_var($int_hex, FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_ALLOW_HEX);
echo "<pre><b>FILTER_FLAG_ALLOW_HEX (int filters only)</b><br />".$allow_hex."</pre>";


/* strip low - strips ascii < 32 */
$strip_low = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
echo "<pre><b>FILTER_FLAG_STRIP_LOW</b><br />".$strip_low."</pre>";


/* strip high - strips ascii > 127 */
$strip_high = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
echo "<pre><b>FILTER_FLAG_STRIP_HIGH</b><br />".$strip_high."</pre>";


/* encode low - encodes ascii < 32 */
$enc_low = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
echo "<pre><b>FILTER_FLAG_ENCODE_LOW</b><br />".$enc_low."</pre>";


/* encode high - encodes ascii > 127 */
$enc_high = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
echo "<pre><b>FILTER_FLAG_ENCODE_HIGH</b><br />".$enc_high."</pre>";


/* don't encode ' or " */
$deq = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
echo "<pre><b>FILTER_FLAG_NO_ENCODE_QUOTES</b><br />".$deq."</pre>";


/* allow fractions (for number_float filters) */
$fract = filter_var($string, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
echo "<pre><b>FILTER_FLAG_ALLOW_FRACTION (*_number_float filters only)</b><br />".$fract."</pre>";


/* allow thousands (for number_float filters) */
$thous = filter_var($string, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
echo "<pre><b>FILTER_FLAG_ALLOW_THOUSAND (*_number_float filters only)</b><br />".$thous."</pre>";


/* allow scientific notation (for number_float filters) */
$scient = filter_var($string, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_SCIENTIFIC);
echo "<pre><b>FILTER_FLAG_ALLOW_SCIENTIFIC (*_number_float filters only)</b><br />".$scient."</pre>";


/* scheme_required - require query in validate_url */
$schemer = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
echo "<pre><b>FILTER_FLAG_SCHEME_REQUIRED (validate_url filters only)</b><br />";
if ($schemer !== false)
{
	echo "URL is valid (including scheme)</pre>";
} else {
	echo "Invalid URL (no scheme)</pre>";
}



/* host_required - require host in validate_url */
$hostr = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
echo "<pre><b>FILTER_FLAG_HOST_REQUIRED (validate_url filters only)</b><br />";
if ($hostr !== false)
{
	echo "URL is valid (including host)</pre>";
} else {
	echo "Invalid URL (no host)</pre>";
}



/* path_required - require path in validate_url */
$pathr = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
echo "<pre><b>FILTER_FLAG_PATH_REQUIRED (validate_url filters only)</b><br />";
if ($pathr !== false)
{
	echo "URL is valid (including path)</pre>";
} else {
	echo "Invalid URL (no path)</pre>";
}



/* query_required - require query in validate_url */
$queryr = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED);
echo "<pre><b>FILTER_FLAG_QUERY_REQUIRED (validate_url filters only)</b><br />";
if ($queryr !== false)
{
	echo "URL is valid (including query)</pre>";
} else {
	echo "Invalid URL (no query)</pre>";
}



/* ipv4 - allow only ipv4 for validate_ip */
$ipv4r = filter_var($ipaddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
echo "<pre><b>FILTER_FLAG_IPV4 (validate_ip filters only)</b><br />";
if ($ipv4r !== false)
{
	echo "IP Address is valid (IPv4)</pre>";
} else {
	echo "IP Address is invalid (IPv4)</pre>";
}



/* ipv6 - allow only ipv6 for validate_ip */
$ipv6r = filter_var($ipv6addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
echo "<pre><b>FILTER_FLAG_IPV^ (validate_ip filters only)</b><br />";
if ($ipv6r !== false)
{
	echo "IP Address is valid (IPv6)</pre>";
} else {
	echo "IP Address is invalid (IPv6)</pre>";
}



/* no_res_range - deny for reserved IP addresses for validate_ip */
$iprr = filter_var($ipres, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE);
echo "<pre><b>FILTER_FLAG_NO_RES_RANGE^ (validate_ip filters only)</b><br />";
if ($iprr !== false)
{
	echo "IP Address is valid (not reserved)</pre>";
} else {
	echo "IP Address is invalid (reserved)</pre>";
}



/* no_priv_range - deny for private IP addresses for validate_ip */
$ippr = filter_var($ipres, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
echo "<pre><b>FILTER_FLAG_NO_RES_RANGE^ (validate_ip filters only)</b><br />";
if ($ippr !== false)
{
	echo "IP Address is valid (not private)</pre>";
} else {
	echo "IP Address is invalid (private)</pre>";
}
?>
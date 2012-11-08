<?php
/**
 * receive mail via the email_message class
 *
 * @package default
 * @author Elias Müller
 **/
require_once('classes/mail/email_message.php');
require_once("classes/mail/smtp_message.php");
require_once("classes/mail/smtp.php");
require_once('classes/mail/sasl.php');

class MailSend {
	/**
	 * instance of the smtp class
	 *
	 * @var object
	 **/
	private $mail;
	
	/**
	 * config vars
	 *
	 * @var array
	 **/
	private $config;
	
	/**
	 * quoted text, i.g. for message reply
	 *
	 * @var array
	 **/
	private $quote;
	
	/**
	 * set all params from config file
	 */
	public function __construct() {
		$s = cBootstrap::getInstance();
		$this->config = $s->getConfig('mail');
		$this->mail = new smtp_message_class;
		$this->mail->localhost = "localhost";
		$this->mail->default_charset = 'UTF-8';
		$this->mail->smtp_host = $this->config['smtp'];
		$this->mail->smtp_port = $this->config['smtp_port'];
		$this->mail->smtp_user = $this->config['smtp_user'];
		$this->mail->smtp_password = $this->config['smtp_password'];
		$this->mail->smtp_port = $this->config['smtp_port'];
		$this->mail->smtp_ssl = 1;
	} 

	/**
	 * set message content
	 *
	 * @param string $content the email message
	 * @param string $type 'text' or 'html'
	 * @return void
	 * @author Elias Müller
	 **/
	public function addContent($content, $type = 'text') {
		//$content = utf8_decode($content);
		$content .= '<br /><p>-----------------<br />Bitte die Nummer im Betreff bei Antwort nicht entfernen, damit die Mail der richtigen Belegung zugeordnet werden kann.</p>';
		if ($type == 'text') {
			$this->mail->AddQuotedPrintableTextPart($this->mail->WrapText(strip_tags($content)).$this->quote['text']);
		} else {
			$this->mail->CreateQuotedPrintableHTMLPart($content.$this->quote['html'],"",$h);
			$this->mail->CreateQuotedPrintableTextPart($this->mail->WrapText($this->convert($content)).$this->quote['text'],"",$t);
			$alt = array($t,$h);
			$this->mail->AddAlternativeMultipart($alt);
		}
	}
	
	/**
	 * add quoted message
	 *
	 * @param string $content the text to quote
	 * @param string $sender name of the sender
	 * @param array $dt date and time
	 * @return void
	 * @author Elias Müller
	 **/
	public function addQuote($content, $sender, $dt) {
		$header = 'Am '.$dt['date']->returnDate('d.m.Y').' um '.$dt['time'].' schrieb '.$sender.':';
		$this->quote['html'] = '<p>'.$header.'</p><blockquote type="cite">'.$content.'</blockquote>';
		$this->quote['text'] = $header."\n".$this->mail->QuoteText($this->mail->WrapText($this->convert($content)));
	}
	
	/**
	 * add file attachment
	 *
	 * @param string $path path of the file
	 * @return bool
	 * @author Elias Müller
	 **/
	public function addAttachment($path, $name = '') {
		if (is_file($path)) { // check if file exists
			$attachment=array(
				"FileName"=>$path,
				"Content-Type"=>"automatic/name",
				"Disposition"=>"attachment"
			);
			if ($name != '')
				$attachment['Name'] = $name;
			$this->mail->AddFilePart($attachment);
			return true;
		}
		return false;
	}

	/**
	 * send mail
	 *
	 * @param string $subject message subject
	 * @param array $from sender name and adress
	 * @param array $to receiver name and adress
	 * @return bool
	 * @author Elias Müller
	 **/
	public function send($subject, $from, $to) {
		if ($subject == '') {
			Error::addError('Kein Mail-Versand ohne Betreff!');
			return false;
		}

		$this->mail->SetEncodedEmailHeader("To",$to['email'],$to['name']);
		$this->mail->SetEncodedEmailHeader("From",$from['email'],$from['name']);
		$this->mail->SetEncodedEmailHeader("Reply-To",'babel@jugendburg-balduinstein.de','Jugendburg Balduinstein');
		$this->mail->SetHeader("Return-Path",'elias.mueller@rwth-aachen.de');
		$this->mail->SetEncodedEmailHeader("Errors-To",'elias.mueller@rwth-aachen.de','Elias Müller');
		$this->mail->SetEncodedHeader("Subject",$subject);
		
		if (($error = $this->mail->Send()) == '')
			return true;
		
		Error::addError("Fehler beim Mail-Versand: ".HtmlSpecialChars($error), true);
		return false;
	}
	
	/**
	 * convert a message from HTML to plain text
	 *
	 * @return string the convertet message
	 * @author Elias Müller
	 **/
	private function convert($text) {
		$text = str_replace('<br />', "\n", $text);
		$text = str_replace('</p>', "\n\n", $text);
		$text = str_replace('</li>', "\n", $text);
		$text = str_replace('</ul>', "\n", $text);
		$text = str_replace('</ol>', "\n", $text);
		$text = str_replace('<li>', "- ", $text);
		return strip_tags($text);
	}
} // END class 
?>
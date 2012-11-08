<?php
require_once('classes/tcpdf/config/lang/ger.php');
require_once('classes/tcpdf/tcpdf.php');

class PDF extends TCPDF {
    // Page header
    public function Header() {
		$s = cBootstrap::getInstance();
		$config = $s->getConfig('pdf');
		
        // Logo
        $this->Image($config['header']['logo'], 15, 12, 85, '', 'PNG', '', 'T', true, 300);
        // Title
		$this->SetFont($config['font']['header'], '', $config['size']['header']);
		$this->SetX(117);
        $this->MultiCell(100, 15, $config['header']['text'], 0, 'L', false, 0, 117, 10.5);
		// ending header line
		$this->Line(4, 42, 206, 42, array('width' => 1, 'color' => array(147,0,0)));
    }

	// Error handling
	public function Error($msg) {
		// unset all class variables
		$this->_destroy(true);
		// exit program and print error
		// print the error message
        Error::addError('TCPDF ERROR: '.$msg, true);
		//die("TCPDF error!");
	}
}
?>
<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class Bestellung for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 5
 *
 * @category File
 * @package  Pizzaservice
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 * @license  http://www.h-da.de  none 
 * @Release  1.2 
 * @link     http://www.fbi.h-da.de 
 */

// to do: change name 'Bestellung' throughout this file
require_once './Page.php';
require_once './blocks/Speisekarte.php';
require_once './blocks/Warenkorb.php';

/**
 * This is a template for top level classes, which represent 
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class. 
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking 
 * during implementation.
 
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 */
class Bestellung extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    
	/**
     * @var Speisekarte
     */
    private $_speisekarte;

    /**
     * @var Warenkorb
     */
    private $_warenkorb;

    /**
     * @var array
     */
    private $_pizzen;
	
	
    /**
     * Instantiates members (to be defined above).   
     * Calls the constructor of the parent i.e. page class.
     * So the database connection is established.
     *
     * @return none
     */
    protected function __construct() 
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
		 // Initialize members
        $this->_speisekarte = new Speisekarte($this->_database);
        $this->_warenkorb   = new Warenkorb($this->_database);
    }
    
    /**
     * Cleans up what ever is needed.   
     * Calls the destructor of the parent i.e. page class.
     * So the database connection is closed.
     *
     * @return none
     */
    protected function __destruct() 
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return none
     */
    protected function getViewData()
    {
        $stmt = $this->_database->prepare('SELECT id, name, preis, bild
                                         FROM angebot');
      if ($stmt->execute()) {
        $stmt->bind_result($id, $name, $price, $image);

        while ($stmt->fetch()) {
          $this->_pizzen[] = array(
            'id'    => $id,
            'name'  => $name,
            'price' => $price,
            'image' => $image
          );
        }
      }
    }
    
    /**
     * First the necessary data is fetched and then the HTML is 
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if avaialable- the content of 
     * all views contained is generated.
     * Finally the footer is added.
     *
     * @return none
     */
    protected function generateView() 
    {
        $this->getViewData();
        $this->generatePageHeader('Bestellung', false);
		
		$this->_speisekarte->generateView('menu', $this->_pizzen);
        $this->_warenkorb->generateView('cart', 'Bestellung.php');
		
        $this->generatePageFooter();
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this page is supposed to do something with submitted
     * data do it here. 
     * If the page contains blocks, delegate processing of the 
	 * respective subsets of data to them.
     *
     * @return none 
     */
    protected function processReceivedData() 
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
		if (isset($_POST['orders'], $_POST['address']) &&
            count($_POST['orders']) > 0 &&
            strlen($_POST['address']) > 0) {
          $stmt = $this->_database->prepare('INSERT INTO bestellung
                  (adresse, zeitpunkt) VALUES (?, CURRENT_TIMESTAMP)');
          $stmt->bind_param('s', $_POST['address']);

          if ($stmt->execute()) {
            $_SESSION['lastOrder'] = $orderId = $this->_database->insert_id;
            $stmt->close();

            $status = 0;
            foreach ($_POST['orders'] as $order) {
              $stmt = $this->_database->prepare('INSERT INTO angebot_bestellung
                      (angebot_id, bestellung_id, status)
                      VALUES (?, ?, ?)');
              $stmt->bind_param('iii', $order, $orderId, $status); //order is angebot id, orderid is bestellung_id, and status is status
              $stmt->execute();
              $stmt->close();
            }

            header('Location: Kunde.php');
          }
        }
    }

    /**
     * This main-function has the only purpose to create an instance 
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     *
     * @return none 
     */    
    public static function main() 
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
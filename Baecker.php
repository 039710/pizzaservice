<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class Baekcer for the exercises of the EWA lecture
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

// to do: change name 'Baekcer' throughout this file
require_once './Page.php';
require_once './blocks/Statustabelle.php';
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
class Baecker extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
     /**
     * @var Statustabelle
     */
    private $statusTabelle;
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
        $this->statusTabelle = new Statustabelle($this->_database);
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
    { //modify here  <-------------------------------------------------------
        $stmt = $this->_database->prepare('SELECT
        angebot.name, angebot.id, angebot_bestellung.id,
        angebot_bestellung.status, angebot_bestellung.bestellung_id
        FROM angebot_bestellung
        INNER JOIN angebot ON angebot.id = angebot_bestellung.angebot_id');
	  // <-------------------------------------------------------
      if ($stmt->execute()) {
        $stmt->bind_result($name, $supplyId, $id, $status, $orderId);
        $this->_orders = array();

        while ($stmt->fetch()) {
          if (!isset($this->_orders[$orderId])) {
            $this->_orders[$orderId] = array();
          }

          $this->_orders[$orderId][$id] = array(
            'id'     => $supplyId,
            'name'   => $name,
            'status' => $status
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
        $this->generatePageHeader('Bäcker');
		
		$columns = array('bestellt', 'im Ofen', 'fertig');
        $url = 'Baecker.php';

        echo '<form action="' . $url . '" method="POST">';
        foreach ($this->_orders as $order) {
          $this->statusTabelle->generateView(null, null, $columns,
                                             $order, true);
          echo '<hr>' . PHP_EOL;
        }
        echo '</form>' . PHP_EOL;
		
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

        if (isset($_POST['order'])) {
          $stmt = $this->_database->prepare('UPDATE angebot_bestellung
            SET status = ?
            WHERE id = ?');
		
			$message = "status changed";
            echo "<script type='text/javascript'>alert('$message');</script>";
			
          foreach ($_POST['order'] as $id => $status) {  //loop id and status from order
            $stmt->bind_param('ii', $status, $id); // bind status and id with type int
            $stmt->execute();
          }

          // Check if an order is finished
          $stmt = $this->_database->prepare('SELECT bestellung_id, status
            FROM angebot_bestellung');

          if ($stmt->execute()) {
            $orders = array(); // create array orders

            $stmt->bind_result($orderId, $status); // bind result

            while ($stmt->fetch()) { // do fetching from statement
              // Create entry if not already exists
              if (!isset($orders[$orderId])) { // if orders is not exist set orders[$orderId] = true
                $orders[$orderId] = true;
              }

              // if Status 2 then finished
              $orders[$orderId] = $orders[$orderId] && $status == 2; // true = (true && (something == 2));
            }

			
			// set all bestellung status to 0 -> bestellt
            $stmt = $this->_database->prepare('UPDATE bestellung 
              SET status = 0
              WHERE id = ? AND status IS NULL');

            foreach ($orders as $id => $finished) {
              if ($finished) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
              }
            }
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
            $page = new Baecker();
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
Baecker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
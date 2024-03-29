<?php	// UTF-8 marker äöüÄÖÜß€
/**
 * Class Fahrer for the exercises of the EWA lecture
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

// to do: change name 'Fahrer' throughout this file
require_once './Page.php';
require_once './blocks/DetailInfo.php';

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
class Fahrer extends Page
{
	private $orders = array();
    // to do: declare reference variables for members 
    // representing substructures/blocks
    
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
        $stmt = $this->_database->prepare('SELECT angebot.name, angebot.preis,
        bestellung.id, bestellung.adresse, bestellung.status
        FROM angebot_bestellung
        INNER JOIN angebot ON angebot.id = angebot_bestellung.angebot_id
        INNER JOIN bestellung
          ON bestellung.id = angebot_bestellung.bestellung_id
        WHERE bestellung.status >= 0
        ORDER BY bestellung.id');

      if ($stmt->execute()) {
        $stmt->bind_result($name, $price, $id, $address, $status);
        $this->_orders = array();

        // This values will be resetted on every new order
        $currentOrder = 0;
        while ($stmt->fetch()) {
          if ($id != $currentOrder) {
            $this->_orders[$id] = array(
              'list'   => '',
              'price'  => 0,
              'address' => $address,
              'status' => $status
            );
            $currentOrder = $id;
          }

          // Add a comma
          if (strlen($this->_orders[$id]['list']) > 0) {
            $this->_orders[$id]['list'] .= ', ';
          }

          $this->_orders[$id]['list']  .= $name;
          $this->_orders[$id]['price'] += $price;
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
        $this->generatePageHeader('Fahrer');
		
		echo <<<EOF
        <form class="order" action="Fahrer.php" method="POST">
EOF;
        foreach ($this->_orders as $key => $order) {
          $last = $key == count($this->_orders) - 1;
          $info = new DetailInfo($this->_database);
          $info->generateView(null, $key, $order, !$last);
        }
        echo <<<EOF
        </form>
EOF;

		
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
        if (isset($_POST)) {
          $stmt = $this->_database->prepare('UPDATE bestellung
            SET status = ?
            WHERE id = ?');

          foreach ($_POST as $id => $status) {
            $stmt->bind_param('ii', $status, $id);
            $stmt->execute();
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
            $page = new Fahrer();
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
Fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
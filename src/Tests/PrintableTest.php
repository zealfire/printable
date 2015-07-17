<?php
 
namespace Drupal\printable\Tests;
 
use Drupal\simpletest\WebTestBase;
 
/**
 * Tests the printable module functionality
 *
 * @group printable
 */
class PrintableTest extends WebTestBase {
 
  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('printable', 'node', 'block');
 
  /**
   * A simple user with 'access content' permission
   */
  private $user;
 
  /**
   * Perform any initial set up tasks that run before every test method
   */
  public function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser(array('access content'));
  }
}
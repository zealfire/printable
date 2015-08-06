<?php
 
namespace Drupal\printable\Tests;
 
use Drupal\Core\Database\Database;
use Drupal\node\Tests\NodeTestBase;
 
/**
 * Tests the printable module functionality
 *
 * @group printable
 */
class PrintablePageTest extends NodeTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('printable', 'node_test_exception', 'dblog', 'system');
 
  /**
   * Perform any initial set up tasks that run before every test method
   */
  public function setUp() {
    parent::setUp();
    $web_user = $this->drupalCreateUser(array('create page content', 'edit own page content', 'view printer friendly versions', 'administer printable'));
    $this->drupalLogin($web_user);
  }

  /**
   * Tests that the 'printable/print/node/{node}' path returns the right content
   */
  public function testCustomPageExists() {
    global $base_url;
    $node_type_storage = \Drupal::entityManager()->getStorage('node_type');

    // Test /node/add page with only one content type.
    $node_type_storage->load('article')->delete();
    $this->drupalGet('node/add');
    $this->assertResponse(200);
    $this->assertUrl('node/add/page');
    // Create a node.
    $edit = array();
    $edit['title[0][value]'] = $this->randomMachineName(8);
    $bodytext = $this->randomMachineName(16) . 'This is functional test which I am writing for printable module.';
    $edit['body[0][value]'] = $bodytext;
    $this->drupalPostForm('node/add/page', $edit, t('Save'));

    // Check that the Basic page has been created.
    $this->assertRaw(t('!post %title has been created.', array('!post' => 'Basic page', '%title' => $edit['title[0][value]'])), 'Basic page created.');

    // Check that the node exists in the database.
    $node = $this->drupalGetNodeByTitle($edit['title[0][value]']);
    $this->assertTrue($node, 'Node found in database.');

    // Verify that pages do not show submitted information by default.
    $this->drupalGet('node/' . $node->id());
    $this->assertResponse(200);
    // see https://api.drupal.org/api/drupal/core%21modules%21simpletest%21src%21AssertContentTrait.php/trait/AssertContentTrait/8
    $this->drupalGet('printable/print/node/' . $node->id());
    $this->assertResponse(200);
    // Checks the presence of title in the page.
    $this->assertRaw($edit['title[0][value]'], 'Title discovered successfully in the printable page');
    // Checks the presence of image in the header.
    $this->assertRaw(theme_get_setting('logo.url'), 'Image discovered successfully in the printable page');
    // Checks the presence of body in the page.
    $this->assertRaw($edit['body[0][value]'], 'Body discovered successfully in the printable page');
    // Check if footer is rendering correctly.
    $this->assertNoRaw($base_url. 'node/' . $node->id(), 'Source Url not discovered in the printable page');
    $this->verbose($base_url);
    // Enable the option of showing links present in the footer of page.
    $this->drupalGet('admin/config/user-interface/printable/print');
    $this->drupalPostForm(NULL, array(
      'print_html_display_sys_urllist' => 1
    ), t('Submit'));
    $this->drupalGet('admin/config/user-interface/printable/pdf');
    $this->assertResponse(200);

    $this->assertNoRaw($base_url. 'node/' . $node->id(), 'Source Url discovered in the printable page');
  }

}

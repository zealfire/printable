<?php
 
namespace Drupal\printable\Tests;
 
use Drupal\Core\Database\Database;
use Drupal\node\Tests\NodeTestBase;
 
/**
 * Tests the printable_pdf module functionality
 *
 * @group printable
 */
class PrintablePdfTest extends NodeTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('printable', 'printable_pdf', 'node_test_exception', 'dblog', 'system');
 
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

    // Set the PDF generating tool. 
    $this->drupalGet('admin/config/user-interface/printable/pdf');
    $this->drupalPostForm(NULL, array(
      'print_pdf_pdf_tool' => 'mPDF'
    ), t('Submit'));
    $this->drupalGet('admin/config/user-interface/printable/pdf');
    $this->assertResponse(200);

    $config = $this->config('printable.settings');
    $this->verbose($config->get('printable.pdf_tool'));
    // Test whether PDF page is being generated.
    $this->drupalGet('printable/pdf/node/' . $node->id());
    $this->assertResponse(200);
  }

}

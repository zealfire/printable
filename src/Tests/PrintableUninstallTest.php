<?php

/**
 * @file
 * Contains \Drupal\printable\Tests\PrintableUninstallTest.
 */

namespace Drupal\printable\Tests;

use Drupal\Core\Database\Database;
use Drupal\node\Tests\NodeTestBase;

/**
 * Tests the whether printable module uninstall successfully.
 *
 * @group printable
 */
class PrintableUninstallTest extends NodeTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('printable', 'node_test_exception', 'dblog');

  /**
   * Perform any initial set up tasks that run before every test method.
   */
  public function setUp() {
    parent::setUp();
    $web_user = $this->drupalCreateUser(array('create page content',
      'edit own page content',
      'view printer friendly versions',
      ));
    $this->drupalLogin($web_user);
  }

  /**
   * Tests that the printable/print/node/{node} path returns the right content.
   */
  public function testCustomPageExists() {
    $node_type_storage = \Drupal::entityManager()->getStorage('node_type');

    // Test /node/add page with only one content type.
    $node_type_storage->load('article')->delete();
    $this->drupalGet('node/add');
    $this->assertResponse(200);
    $this->assertUrl('node/add/page');
    // Create a node.
    $edit = array();
    $edit['title[0][value]'] = $this->randomMachineName(8);
    $edit['body[0][value]'] = $this->randomMachineName(16);
    $this->drupalPostForm('node/add/page', $edit, t('Save'));

    // Check that the Basic page has been created.
    $this->assertRaw(t('!post %title has been created.', array('!post' => 'Basic page', '%title' => $edit['title[0][value]'])), 'Basic page created.');

    // Check that the node exists in the database.
    $node = $this->drupalGetNodeByTitle($edit['title[0][value]']);
    $this->assertTrue($node, 'Node found in database.');

    // Verify that pages do not show submitted information by default.
    $this->drupalGet('node/' . $node->id());
    $this->assertResponse(200);

    $this->drupalGet('printable/print/node/' . $node->id());
    $this->assertResponse(200);
    // Uninstall the printable module and check the printable version of node
    // is also deleted.
    \Drupal::service('module_installer')->uninstall(array('printable'));
    $this->drupalGet('printable/print/node/' . $node->id());
    $this->assertResponse(404);
  }

}

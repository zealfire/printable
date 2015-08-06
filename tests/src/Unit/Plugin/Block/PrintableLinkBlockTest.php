<?php

/**
 * @file
 * Contains Drupal\Tests\printable\Unit\Plugin\Block\PrintableLinkBlockTest.
 */

namespace Drupal\Tests\printable\Unit\Plugin\Block;

use Drupal\Tests\UnitTestCase;
use Drupal\printable\Plugin\Block\PrintableLinksBlock;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Tests the printable links block plugin.
 *
 * @group Printable
 */
class PrintableLinkBlockTest extends UnitTestCase {

  protected $configuration = array();

  protected $pluginId;

  protected $pluginDefinition = array();

  public function __construct() {
    parent::__construct();
    $this->pluginId = 'printable_links_block:node';
    $this->pluginDefinition['module'] = 'printable';
    $this->pluginDefinition['provider'] = '';
  }

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Printable Block',
      'descriptions' => 'Tests the printable block plugin class.',
      'group' => 'Printable',
    );
  }

  /**
   * Tests the block build method.
   *
   * @covers PrintableLinksBlock::build
   */
  public function testBuild() {
    $routematch = $this->getMockBuilder('Drupal\Core\Routing\CurrentRouteMatch')
      ->disableOriginalConstructor()
      ->setMethods(array('getMasterRouteMatch', 'getParameter'))
      ->getMock();
    $routematch->expects($this->exactly(2))
      ->method('getMasterRouteMatch')
      ->will($this->returnSelf());
    $routematch->expects($this->exactly(2))
      ->method('getParameter')
      ->will($this->returnValue($this->getMock('Drupal\Core\Entity\EntityInterface')));
    $links = array(
      'title' => 'Print',
      'url' => '/printable/print/foo/1',
      'attributes' => array(
        'target' => '_blank',
      ),
    );
    $links_builder = $this->getMockBuilder('Drupal\printable\PrintableLinkBuilderInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $links_builder->expects($this->once())
      ->method('buildLinks')
      ->will($this->returnValue($links));

    $block = new PrintableLinksBlock($this->configuration, $this->pluginId, $this->pluginDefinition, $routematch, $links_builder);

    $expected_build = array(
      '#theme' => 'links__entity__printable',
      '#links' => $links,
    );
    $this->assertEquals($expected_build, $block->build());
  }
}

<?php

/**
 * @file
 * Contains Drupal\Tests\printable\Unit\PrintableLinkBuilderTest
 */

namespace Drupal\Tests\printable\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\printable\PrintableLinkBuilder;
use Drupal\Core\Url;

/**
 * Tests the print format plugin.
 *
 * @group Printable
 */
class PrintableLinkBuilderTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Printable Link Builder',
      'descriptions' => 'Tests the printable link builder class.',
      'group' => 'Printable'
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Tests generating the render array of printable links.
   *
   * @covers PrintableLinkBuilder::BuildLinks
   */
  public function testBuildLinks() {
    $definitions = array(
      'foo' => array(
        'title' => 'Foo',
      ),
      'bar' => array(
        'title' => 'Bar',
      ),
    );
    $entity_type = 'node';
    $entity_id = rand(1, 100);

    $config = $this->getConfigFactoryStub(array('printable.settings' => array('open_target_blank' => TRUE)));

    $printable_manager = $this->getMockBuilder('Drupal\printable\PrintableFormatPluginManager')
      ->disableOriginalConstructor()
      ->getMock();
    $printable_manager->expects($this->once())
      ->method('getDefinitions')
      ->will($this->returnValue($definitions));

    $link_builder = new PrintableLinkBuilder($config, $printable_manager);

    $entity = $this->getMock('Drupal\Core\Entity\EntityInterface');
    $entity->expects($this->exactly(2))
      ->method('getEntityTypeId')
      ->will($this->returnValue($entity_type));
    $entity->expects($this->exactly(2))
      ->method('id')
      ->will($this->returnValue($entity_id));

    $links = $link_builder->buildLinks($entity);
    $this->assertEquals(2, count($links));
    foreach($definitions as $key => $definition) {
      $link = $links[$key];
      $this->assertEquals($definition['title'], $link['title']);
      $this->assertEquals(Url::fromRoute('printable.show_format.' . $entity_type, array('printable_format' => $key, 'entity' => $entity_id)), $link['url']);
      $this->assertEquals('_blank', $link['attributes']['target']);
    }
  }
}

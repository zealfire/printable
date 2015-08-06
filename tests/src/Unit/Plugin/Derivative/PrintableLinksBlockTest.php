<?php

/**
 * @file
 * Contains \Drupal\printable\Tests\Plugin\Derivative\PrintableLinksBlockTest.
 */

namespace Drupal\Tests\printable\Unit\Plugin\Derivative;

use Drupal\Tests\UnitTestCase;
use Drupal\printable\Plugin\Derivative\PrintableLinksBlock;

/**
 * Tests the printable links block plugin derivative..
 *
 * @group Printable
 */
class PrintableLinksBlockTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Printable Block Derivative',
      'descriptions' => 'Tests the printable block plugin derivative class.',
      'group' => 'Printable',
    );
  }

  /**
   * Tests getting the plugin label from the plugin.
   *
   * @covers PrintableLinksBlock::GetDerivativeDefinitions
   */
  public function testGetDerivativeDefinitions() {
    $entity_definition = $this->getMockBuilder('Drupal\Core\Entity\EntityType')
      ->disableOriginalConstructor()
      ->getMock();

    $printable_format_manager = $this->getMockBuilder('Drupal\printable\PrintableEntityManager')
      ->disableOriginalConstructor()
      ->getMock();
    $printable_format_manager->expects($this->once())
      ->method('getPrintableEntities')
      ->will($this->returnValue(array(
        'foo' => $entity_definition,
        'bar' => $entity_definition,
      )));

    $entity_definition->expects($this->at(0))
      ->method('getLabel')
      ->will($this->returnValue('Foo'));
    $entity_definition->expects($this->at(1))
      ->method('getLabel')
      ->will($this->returnValue('Bar'));
    $derivative = new PrintableLinksBlock($printable_format_manager);
    $base_plugin_definition = array(
      'admin_label' => 'Printable Links Block'
    );

    $expected = array(
      'foo' => array(
        'admin_label' => 'Printable Links Block (Foo)',
      ),
      'bar' => array(
        'admin_label' => 'Printable Links Block (Bar)',
      ),
    );
    $this->assertEquals($expected, $derivative->getDerivativeDefinitions($base_plugin_definition));
  }
}

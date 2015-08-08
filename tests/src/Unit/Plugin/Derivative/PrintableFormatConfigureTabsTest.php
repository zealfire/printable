<?php

/**
 * @file
 * Contains \Drupal\printable\Tests\Plugin\Derivative\PrintableFormatConfigureTabsTest
 */

namespace Drupal\Tests\printable\Unit\Plugin\Derivative;

use Drupal\Tests\UnitTestCase;
use Drupal\printable\Plugin\Derivative\PrintableFormatConfigureTabs;

/**
 * Tests the printable configuration tabs plugin derivative.
 *
 * @group Printable
 */
class PrintableFormatConfigureTabsTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Printable Tabs Plugin Derivative',
      'descriptions' => 'Tests the printable tabs plugin derivative class.',
      'group' => 'Printable',
    );
  }

  /**
   * Tests getting the plugin label from the plugin.
   *
   * @covers PrintableFormatConfigureTabs::GetDerivativeDefinitions
   */
  public function testGetDerivativeDefinitions() {
    $printable_format_manager = $this->getMockBuilder('Drupal\printable\PrintableFormatPluginManager')
      ->disableOriginalConstructor()
      ->getMock();
    $printable_format_manager->expects($this->once())
      ->method('getDefinitions')
      ->will($this->returnValue(array(
        'foo' => array(
          'title' => 'Foo',
        ),
        'bar' => array(
          'title' => 'Bar',
        ),
      )));
    $derivative = new PrintableFormatConfigureTabs($printable_format_manager);

    $expected = array(
      'foo' => array(
        'title' => 'Foo',
        'route_parameters' => array('printable_format' => 'foo'),
        'route_name' => 'printable.format_configure_foo', 
      ),
      'bar' => array(
        'title' => 'Bar',
        'route_parameters' => array('printable_format' => 'bar'),
        'route_name' => 'printable.format_configure_bar',
      ),
    );
    $this->assertEquals($expected, $derivative->getDerivativeDefinitions(array()));
  }
}

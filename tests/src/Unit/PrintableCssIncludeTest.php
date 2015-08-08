<?php

/**
 * @file
 * Contains Drupal\Tests\printable\Unit\PrintableCssIncludeTest;
 */

namespace Drupal\Tests\printable\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\printable\PrintableCssInclude;

/**
 * Tests the print format plugin.
 *
 * @group Printable
 */
class PrintableCssIncludeTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Printable CSS Include',
      'descriptions' => 'Tests the printable CSS include class.',
      'group' => 'Printable'
    );
  }

  /**
   * Tests getting the plugin label from the plugin.
   *
   * @covers PrintableCssInclude::getCssIncludePath
   *
   * @dataProvider providerTestGetCssIncludePath
   */
  public function testGetCssIncludePath($include, $expected) {
    $config = $this->getConfigFactoryStub(array('printable.settings' => array('css_include' => $include)));

    $theme_info = array(
      'bartik' => new \stdClass(),
    );
    $theme_info['bartik']->uri = 'core/themes/bartik/bartik.info.yml';
    $theme_handler = $this->getMockBuilder('Drupal\Core\Extension\ThemeHandlerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $theme_handler->expects($this->any())
      ->method('listInfo')
      ->will($this->returnValue($theme_info));

    $css_include = new PrintableCssInclude($config, $theme_handler);

    $this->assertEquals($expected, $css_include->getCssIncludePath());
  }

  /**
   * Data provider for testGetCssIncludePath().
   */
  public function providerTestGetCssIncludePath() {
    return array(
      array('[theme:bartik]/css/test.css', 'core/themes/bartik/css/test.css'),
      array('[theme:foobar]/css/test.css', '/css/test.css'),
      array('foo/bar/css/test.css', 'foo/bar/css/test.css'),
    );
  }
}

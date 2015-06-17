<?php

/**
 * @file
 * Contains \Drupal\printable\LinkExtractor\InlineLinkExtractor.
 */

namespace Drupal\printable\LinkExtractor;

use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\printable\LinkExtractor\LinkExtractorInterface;
use wa72\htmlpagedom\HtmlPageCrawler;

/**
 * Link extractor.
 */
class InlineLinkExtractor implements LinkExtractorInterface {

  /**
   * The DomCrawler object.
   *
   * @var \Wa72\HtmlPageDom\HtmlPageCrawler
   */
  protected $crawler;

  /**
   * The URL generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new InlineLinkExtractor object.
   */
  public function __construct(HtmlPageCrawler $crawler, UrlGeneratorInterface $url_generator) {
    $this->crawler = $crawler;
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public function extract($string) {
    $this->crawler->addContent($string);

    $this->crawler->filter('a')->each(function(HtmlPageCrawler $anchor, $uri) {
      $href = $anchor->attr('href');
      // This method is deprecated, however it is the correct method to use here
      // as we only have the path.
      $href = $this->urlGenerator->generateFromPath($href, array('absolute' => TRUE));
      $anchor->append(' (' . $href . ')');
    });

    return (string) $this->crawler;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAttribute($content, $attr) {
    $this->crawler->addContent($content);
    $this->crawler->filter('a')->each(function(HtmlPageCrawler $anchor, $uri) {
      $anchor->removeAttribute('href');
    });
    return (string) $this->crawler;
  }

  /**
   * {@inheritdoc}
   */
  public function listAttribute($content) {
    $this->crawler->addContent($content);
    $this->links = "";
    $this->crawler->filter('a')->each(function(HtmlPageCrawler $anchor, $uri) {
      $href = $anchor->attr('href');
      // This method is deprecated, however it is the correct method to use here
      // as we only have the path.
      $href = $this->urlGenerator->generateFromPath($href, array('absolute' => TRUE));
      $this->links .= $href. ",";
    });
    $this->crawler->remove();
    return substr($this->links, 0, strlen($this->links) - 1);
  }

}

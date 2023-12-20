<?php

namespace Drupal\itaiji\Plugin\search_api\processor;

use Drupal\search_api\Processor\FieldsProcessorPluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * @SearchApiProcessor(
 *   id = "custom_character_converter",
 *   label = @Translation("Itaiji"),
 *   description = @Translation("Provides character conversion functionality."),
 *   stages = {
 *     "pre_index_save" = -1,
 *     "preprocess_index" = -1,
 *     "preprocess_query" = -1,
 *   }
 * )
 */
class CustomCharacterConverter extends FieldsProcessorPluginBase {

  protected $logger;
  protected $configFactory;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->configFactory = $container->get('config.factory');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function testType($type) {
    return $this->getDataTypeHelper()->isTextType($type);
  }

  /**
   * {@inheritdoc}
   */
  protected function process(&$value) {
    $value = $this->convertCharacters($value);
  }

  /**
   * Converts specific characters based on configured rules.
   *
   * @param string $value
   *   The string to be processed.
   *
   * @return string
   *   The processed string.
   */
  protected function convertCharacters(string $value): string {
    $conversionRules = $this->configFactory->get('itaiji.settings')->get('conversion_rules');
  
    // Ensure that conversionRules is an array.
    if (!is_array($conversionRules)) {
      return $value;
    }
  
    foreach ($conversionRules as $original => $converted) {
      $value = str_replace($original, $converted, $value);
    }
  
    return $value;
  }
}
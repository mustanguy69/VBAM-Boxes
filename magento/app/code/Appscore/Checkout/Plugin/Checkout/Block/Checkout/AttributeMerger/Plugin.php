<?php
namespace Appscore\Checkout\Plugin\Checkout\Block\Checkout\AttributeMerger;

class Plugin
{
  public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
  {
    if (array_key_exists('street', $result)) {
      $result['street']['children'][0]['placeholder'] = __('Line 1');
      $result['street']['children'][1]['placeholder'] = __('Line 2');
      $result['street']['children'][2]['placeholder'] = __('Line 3');
    }

    if (array_key_exists('firstname', $result)) {
        $result['firstname']['placeholder'] = __('Your first name');
    }

    if (array_key_exists('lastname', $result)) {
        $result['lastname']['placeholder'] = __('Your surname');
    }

    if (array_key_exists('company', $result)) {
        $result['company']['placeholder'] = __('The name of your company');
    }

    if (array_key_exists('city', $result)) {
        $result['city']['placeholder'] = __('e.g Melbourne');
    }

    if (array_key_exists('postcode', $result)) {
        $result['postcode']['placeholder'] = __('Postcode');
    }

    if (array_key_exists('region', $result)) {
        $result['postcode']['placeholder'] = __('Postcode');
    }

    if (array_key_exists('telephone', $result)) {
        $result['telephone']['placeholder'] = __('Mobile or landline');
    }

    return $result;
  }
}
<?php


final class PhabricatorEditEngineSubtype
  extends Phobject {

  const SUBTYPE_DEFAULT = 'default';

  public static function validateSubtypeKey($subtype) {
    if (strlen($subtype) > 64) {
      throw new Exception(
        pht(
          'Subtype "%s" is not valid: subtype keys must be no longer than '.
          '64 bytes.',
          $subtype));
    }

    if (strlen($subtype) < 3) {
      throw new Exception(
        pht(
          'Subtype "%s" is not valid: subtype keys must have a minimum '.
          'length of 3 bytes.',
          $subtype));
    }

    if (!preg_match('/^[a-z]+\z/', $subtype)) {
      throw new Exception(
        pht(
          'Subtype "%s" is not valid: subtype keys may only contain '.
          'lowercase latin letters ("a" through "z").',
          $subtype));
    }
  }

  public static function validateConfiguration($config) {
    if (!is_array($config)) {
      throw new Exception(
        pht(
          'Subtype configuration is invalid: it must be a list of subtype '.
          'specifications.'));
    }

    $map = array();
    foreach ($config as $value) {
      PhutilTypeSpec::checkMap(
        $value,
        array(
          'key' => 'string',
          'name' => 'string',
        ));

      $key = $value['key'];
      self::validateSubtypeKey($key);

      if (isset($map[$key])) {
        throw new Exception(
          pht(
            'Subtype configuration is invalid: two subtypes use the same '.
            'key ("%s"). Each subtype must have a unique key.',
            $key));
      }

      $map[$key] = true;

      $name = $value['name'];
      if (!strlen($name)) {
        throw new Exception(
          pht(
            'Subtype configuration is invalid: subtype with key "%s" has '.
            'no name. Subtypes must have a name.',
            $key));
      }
    }

    if (!isset($map[self::SUBTYPE_DEFAULT])) {
      throw new Exception(
        pht(
          'Subtype configuration is invalid: there is no subtype defined '.
          'with key "%s". This subtype is required and must be defined.',
          self::SUBTYPE_DEFAULT));
    }
  }

}

<?php

/**
 * StringUtils - Clase utilitaria para manejo de strings
 * 
 * @package Solu_Admin_Utils
 * @since 1.2.0
 * 
 * @example
 * // Usar función de conveniencia (recomendado)
 * $stringUtils = solu_string_utils();
 * 
 * // O crear instancia directamente
 * $stringUtils = new Solu_Admin_Utils_StringUtils();
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Solo definir la clase si no existe ya
if (!class_exists('Solu_Admin_Utils_StringUtils')) {

class Solu_Admin_Utils_StringUtils
{

  /**
   * Limita un string a un número específico de caracteres
   * 
   * @param string $string El string a truncar
   * @param int $length La longitud máxima
   * @param string $suffix El sufijo a añadir si se trunca
   * @return string
   */
  public function truncate($string, $length = 100, $suffix = '...')
  {
    if (strlen($string) <= $length) {
      return $string;
    }

    return substr($string, 0, $length) . $suffix;
  }

  /**
   * Convierte un string a camelCase
   * 
   * @param string $string El string a convertir
   * @return string
   */
  public function toCamelCase($string)
  {
    $string = str_replace(['-', '_', ' '], ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
  }

  /**
   * Convierte un string a PascalCase
   * 
   * @param string $string El string a convertir
   * @return string
   */
  public function toPascalCase($string)
  {
    $string = str_replace(['-', '_', ' '], ' ', $string);
    $string = ucwords($string);
    return str_replace(' ', '', $string);
  }

  /**
   * Convierte un string a snake_case
   * 
   * @param string $string El string a convertir
   * @return string
   */
  public function toSnakeCase($string)
  {
    $string = preg_replace('/[A-Z]/', '_$0', $string);
    $string = strtolower($string);
    return ltrim($string, '_');
  }

  /**
   * Convierte un string a kebab-case
   * 
   * @param string $string El string a convertir
   * @return string
   */
  public function toKebabCase($string)
  {
    $string = preg_replace('/[A-Z]/', '-$0', $string);
    $string = strtolower($string);
    return ltrim($string, '-');
  }

  /**
   * Genera un slug a partir de un string
   * 
   * @param string $string El string a convertir en slug
   * @return string
   */
  public function generateSlug($string)
  {
    // Convertir a minúsculas
    $string = strtolower($string);

    // Reemplazar caracteres especiales
    $string = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $string);
    $string = str_replace(['à', 'è', 'ì', 'ò', 'ù'], ['a', 'e', 'i', 'o', 'u'], $string);

    // Remover caracteres no alfanuméricos
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

    // Reemplazar espacios y guiones múltiples
    $string = preg_replace('/[\s-]+/', '-', $string);

    // Remover guiones al inicio y final
    return trim($string, '-');
  }

  /**
   * Capitaliza la primera letra de cada palabra
   * 
   * @param string $string El string a capitalizar
   * @return string
   */
  public function titleCase($string)
  {
    return ucwords(strtolower($string));
  }

  /**
   * Remueve caracteres especiales y espacios extra
   * 
   * @param string $string El string a limpiar
   * @return string
   */
  public function clean($string)
  {
    // Remover espacios múltiples
    $string = preg_replace('/\s+/', ' ', $string);

    // Remover caracteres de control
    $string = preg_replace('/[\x00-\x1F\x7F]/', '', $string);

    return trim($string);
  }

  /**
   * Verifica si un string contiene solo números
   * 
   * @param string $string El string a verificar
   * @return bool
   */
  public function isNumeric($string)
  {
    return is_numeric($string);
  }

  /**
   * Verifica si un string es un email válido
   * 
   * @param string $email El email a verificar
   * @return bool
   */
  public function isValidEmail($email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

  /**
   * Genera un string aleatorio
   * 
   * @param int $length La longitud del string
   * @param string $chars Los caracteres a usar
   * @return string
   */
  public function random($length = 10, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
  {
    $result = '';
    $charLength = strlen($chars);

    for ($i = 0; $i < $length; $i++) {
      $result .= $chars[rand(0, $charLength - 1)];
    }

    return $result;
  }

  /**
   * Convierte bytes a formato legible
   * 
   * @param int $bytes Los bytes a convertir
   * @param int $precision La precisión decimal
   * @return string
   */
  public function formatBytes($bytes, $precision = 2)
  {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
      $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
  }

  /**
   * Extrae palabras clave de un texto
   * 
   * @param string $text El texto del cual extraer palabras clave
   * @param int $minLength La longitud mínima de las palabras
   * @param int $maxWords El número máximo de palabras
   * @return array
   */
  public function extractKeywords($text, $minLength = 3, $maxWords = 10)
  {
    // Remover caracteres especiales y convertir a minúsculas
    $text = strtolower(preg_replace('/[^\w\s]/', '', $text));

    // Dividir en palabras
    $words = preg_split('/\s+/', $text);

    // Filtrar palabras por longitud mínima
    $words = array_filter($words, function ($word) use ($minLength) {
      return strlen($word) >= $minLength;
    });

    // Contar frecuencia
    $wordCount = array_count_values($words);

    // Ordenar por frecuencia
    arsort($wordCount);

    // Retornar las palabras más frecuentes
    return array_slice(array_keys($wordCount), 0, $maxWords);
  }

  /**
   * Convierte un string a formato de moneda
   * 
   * @param float $amount La cantidad
   * @param string $currency El símbolo de la moneda
   * @param int $decimals El número de decimales
   * @return string
   */
  public function formatCurrency($amount, $currency = '$', $decimals = 2)
  {
    return $currency . number_format($amount, $decimals, '.', ',');
  }

  /**
   * Verifica si un string está vacío o contiene solo espacios
   * 
   * @param string $string El string a verificar
   * @return bool
   */
  public function isEmpty($string)
  {
    return empty(trim($string));
  }

  /**
   * Convierte un string a formato de título (primera letra mayúscula)
   * 
   * @param string $string El string a convertir
   * @return string
   */
  public function capitalize($string)
  {
    return ucfirst(strtolower($string));
  }

  /**
   * Convierte un string a formato de oración (primera letra mayúscula, resto minúsculas)
   * 
   * @param string $string El string a convertir
   * @return string
   */
  public function sentenceCase($string)
  {
    return ucfirst(strtolower($string));
  }

  /**
   * Invierte un string
   * 
   * @param string $string El string a invertir
   * @return string
   */
  public function reverse($string)
  {
    return strrev($string);
  }

  /**
   * Cuenta las palabras en un string
   * 
   * @param string $string El string a contar
   * @return int
   */
  public function wordCount($string)
  {
    return str_word_count($string);
  }

  /**
   * Cuenta los caracteres en un string (sin espacios)
   * 
   * @param string $string El string a contar
   * @return int
   */
  public function charCount($string)
  {
    return strlen(str_replace(' ', '', $string));
  }
}

} // Fin de la verificación class_exists

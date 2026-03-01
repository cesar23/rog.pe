<?php
if (!function_exists('phpConsoleLog')) {
	function phpConsoleLog($data,$label=''){

		if (gettype($data) == "array") {

			echo "<script>";
			if($label!=''){echo "console.group('".$label."');";}
			echo "console.log('debugWP (array):'," . json_encode($data) . ");";
			if($label!=''){echo "console.groupEnd();";}
			echo "</script>";
		} else if (gettype($data) == "object") {


			echo "<script>";
			if($label!=''){echo "console.group('".$label."');";}
			echo "console.log('debugWP (Object):'," . json_encode($data) . ");";
			if($label!=''){echo "console.groupEnd();";}
			echo "</script>";


		} else if (gettype($data) == "integer") {
			echo "<script>";
			if($label!=''){echo "console.group('".$label."');";}
			echo "console.log('debugWP (Integer):'," . json_encode($data) . ");";
			if($label!=''){echo "console.groupEnd();";}
			echo "</script>";


		} else if (gettype($data) == "double") {
			echo "<script>";
			if($label!=''){echo "console.group('".$label."');";}
			echo "console.log('debugWP (double):'," . json_encode($data) . ");";
			if($label!=''){echo "console.groupEnd();";}
			echo "</script>";


		} else {
			echo "<script>";
			if($label!=''){echo "console.group('".$label."');";}
			echo "console.log('debugWP (string):'," . json_encode($data) . ");";
			if($label!=''){echo "console.groupEnd();";}
			echo "</script>";
		}
	}

}
if (!function_exists('isIpv4')) {
	function isIpv4( $ip ) {
		if ( 4 === (int) strlen( inet_pton( $ip ) ) ) {
			return true;
		}
		return false;
	}
}

if (!function_exists('getClientIp_onlyIpv4')) {
	function getClientIp_onlyIpv4() {
		$ipaddress = '';
		if ( isset( $_SERVER['HTTP_CLIENT_IP']) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
			if(!isIpv4($ipaddress)){
				$ipaddress = $_SERVER['REMOTE_ADDR'];

			}
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			if(!isIpv4($ipaddress)){
				$ipaddress = $_SERVER['REMOTE_ADDR'];

			}
		} else if ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			if(!isIpv4($ipaddress)){
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			}
		} else if ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			if(!isIpv4($ipaddress)){
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			}
		} else if ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
			if(!isIpv4($ipaddress)){
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			}
		} else if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}
}

if (!function_exists('helperShortTitle')) {
  /**
   * Acorta un título sin cortar palabras a la mitad.
   *
   * @param string $titulo El título original.
   * @param int $longitud_maxima Longitud máxima permitida (por defecto 30).
   * @return string Título acortado con "..." si fue recortado.
   *
   * Ejemplo de uso:
   *   echo helperShortTitle('Este es un título muy largo que debe ser acortado', 20);
   *   // Salida: "Este es un título..."
   */
  function helperShortTitle($titulo, $longitud_maxima = 30)
  {
    // Si el título ya es corto, lo devolvemos tal cual
    if (strlen($titulo) <= $longitud_maxima) {
      return $titulo;
    }

    // Recortamos el título a la longitud máxima
    $titulo_acortado = substr($titulo, 0, $longitud_maxima);

    // Buscamos el último espacio para no cortar palabras
    $ultimo_espacio = strrpos($titulo_acortado, ' ');

    // Si hay un espacio cerca del final, cortamos ahí
    if ($ultimo_espacio !== false && $ultimo_espacio > ($longitud_maxima - 10)) {
      $titulo_acortado = substr($titulo_acortado, 0, $ultimo_espacio);
    }

    // Retornamos el título acortado y agregamos "..."
    return rtrim($titulo_acortado) . '...';
  }
}
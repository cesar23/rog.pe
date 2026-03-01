/*
version : 1.1
creado : cesar auris
*/

function isInt(value) {
  return (
    !isNaN(value) &&
    parseInt(Number(value)) == value &&
    !isNaN(parseInt(value, 10))
  );
}
//---convierte en entero un string
function convertInt(value) {
  return isInt(value) ? parseInt(value) : "";
}
//---convierte numero humano (1200=1,200)
function humanizeNumber(n) {
  n = n.toString();
  while (true) {
    var n2 = n.replace(/(\d)(\d{3})($|,|\.)/g, "$1,$2$3");
    if (n == n2) break;
    n = n2;
  }
  return n;
}

//quitar null string
function removeNullString(value) {
  return value == null ? "" : value;
}
//---convierte numero en formato con decimales forzados (5=5.00)
function number_format_js(number, decimals, dec_point, thousands_point) {
  //---------- start  mi filtro cesar
  if (typeof number === "number") {
  } else if (typeof number === "string") {
    // si es un string con coma  le quitamos
    number = number.replace(",", "");
  } else if (typeof number === "object") {
    throw new TypeError("es object invalido");
  }
  //---------- end  mi filtro cesar

  if (number == null || !isFinite(number)) {
    throw new TypeError("number is not valid");
  }

  if (!decimals) {
    var len = number.toString().split(".").length;
    decimals = len > 1 ? len : 0;
  }

  if (!dec_point) {
    dec_point = ".";
  }

  if (!thousands_point) {
    thousands_point = ",";
  }

  number = parseFloat(number).toFixed(decimals);
  number = number.replace(".", dec_point);
  var splitNum = number.split(dec_point);
  splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
  number = splitNum.join(dec_point);
  return number;
}

function parseFloatDecimal(numero, decimales) {
  soles_add_porcentaje = parseFloat(numero).toFixed(decimales);
  soles_add_porcentaje = parseFloat(soles_add_porcentaje);
  return soles_add_porcentaje;
}
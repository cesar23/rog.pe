// utils.js

/**
 * Obtiene la fecha actual en formato YYYY-MM-DD
 * @returns {string} Fecha actual en formato YYYY-MM-DD
 */
function getCurrentDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Mes comienza desde 0
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Convierte una fecha en formato YYYY-MM-DD a un objeto Date
 * @param {string} dateStr Fecha en formato YYYY-MM-DD
 * @returns {Date} Objeto Date de la fecha proporcionada
 */
function parseDate(dateStr) {
    const [year, month, day] = dateStr.split('-');
    return new Date(year, month - 1, day); // Mes comienza desde 0
}

/**
 * Calcula la diferencia de días entre dos fechas
 * @param {Date} date1 Primera fecha
 * @param {Date} date2 Segunda fecha
 * @returns {number} Diferencia en días
 */
function daysDifference(date1, date2) {
    const diffTime = Math.abs(date2 - date1);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

/**
 * Añade días a una fecha
 * @param {Date} date Fecha base
 * @param {number} days Número de días a añadir
 * @returns {Date} Nueva fecha con los días añadidos
 */
function addDays(date, days) {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}

/**
 * Obtiene el nombre del mes actual
 * @returns {string} Nombre del mes actual
 */
function getCurrentMonthName() {
    const monthNames = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    const today = new Date();
    return monthNames[today.getMonth()];
}

/**
 * Obtiene el nombre del día actual
 * @returns {string} Nombre del día actual
 */
function getCurrentDayName() {
    const dayNames = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
    const today = new Date();
    return dayNames[today.getDay()];
}


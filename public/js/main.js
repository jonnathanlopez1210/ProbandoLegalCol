/* Gestor Documental Jurídico (SGDJ) - JavaScript principal.
   Fase 1: funcionalidad mínima de la página inicial.
   En fases posteriores se agregará la lógica de los módulos. */

(() => {
    'use strict';

    // Actualiza el año actual en el pie de página.
    const anioActual = new Date().getFullYear();

    document.querySelectorAll('[data-anio]').forEach((elemento) => {
        elemento.textContent = String(anioActual);
    });

    console.info('SGDJ: página inicial cargada correctamente.');
})();
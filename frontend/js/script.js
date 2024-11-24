import { getAll } from './UserApi.js';


async function main() {
    const users = await getAll();
    // Obtener el cuerpo de la tabla
    const tbody = document.querySelector('#tablaUsuarios tbody');

    // Función para insertar filas en la tabla
    users.forEach(usuario => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
      <td>${usuario.nombre}</td>
      <td>${usuario.apellidos}</td>
      <td>${usuario.edad}</td>
    `;
        tbody.appendChild(fila);
    });
}

window.onload = main;
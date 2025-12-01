<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.html");
    exit();
}

// Verificar si el usuario es admin
if($_SESSION['usuario_rol'] !== 'admin'){
    header("Location: ../../controllers/models/public/dashboard.php");
    exit();
}

$nombreUsuario = $_SESSION['usuario_nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Atenea - Sistema de Biblioteca</title>
  <link rel="stylesheet" href="../../assets/css/index.css"> 
</head>

<body>
  <header>
    <div class="logo">
      ATENEA
    </div>

    <nav>
      <button onclick="cambiarPestana('libros')" class="tab-btn active" data-tab="libros">Libros</button>
      <button onclick="cambiarPestana('autores')" class="tab-btn" data-tab="autores">Autores</button>
      <button onclick="cambiarPestana('prestamos')" class="tab-btn" data-tab="prestamos">Préstamos</button>
      <button onclick="cambiarPestana('devoluciones')" class="tab-btn" data-tab="devoluciones">Devoluciones</button>
      <button onclick="cambiarPestana('usuarios')" class="tab-btn" data-tab="usuarios">Usuarios</button>
    </nav>

    <div style="display: flex; align-items: center; gap: 15px;">
      <input type="text" placeholder="Buscar..." class="search-bar" id="searchBar" onkeyup="buscar()">
      <span style="color: white; font-weight: 500;">👤 <?php echo htmlspecialchars($nombreUsuario); ?></span>
      <a href="../../controllers/models/public/logout.php" style="background: #e74c3c; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: 500;">Cerrar Sesión</a>
    </div>
  </header>

  <div class="main-content">
    <div class="table-container">
      <h2 class="section-title" id="sectionTitle">Catálogo de Libros</h2>
      
      <div id="tablaLibros" class="tab-content active">
        <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
              <th>ISBN</th>
                <th>Prestado</th>
                <th>Disponible</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
          <tbody id="tbodyLibros"></tbody>
        </table>
      </div>

      <div id="tablaAutores" class="tab-content">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Nacionalidad</th>
              <th>Fecha Nacimiento</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyAutores"></tbody>
        </table>
      </div>

      <div id="tablaPrestamos" class="tab-content">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Libro</th>
              <th>Fecha Préstamo</th>
              <th>Fecha Devolución</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyPrestamos"></tbody>
        </table>
      </div>

      <div id="tablaDevoluciones" class="tab-content">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Libro</th>
              <th>Fecha Préstamo</th>
              <th>Fecha Devolución Real</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyDevoluciones"></tbody>
        </table>
      </div>

      <div id="tablaUsuarios" class="tab-content">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Rol</th>
              <th>Fecha Registro</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyUsuarios"></tbody>
      </table>
      </div>
    </div>

    <div class="sidebar">
      <button data-modal="modalAñadirLibro" id="btnAddLibro">Añadir Libro</button>
      <button data-modal="modalAñadirAutor" id="btnAddAutor" style="display:none;">Añadir Autor</button>
      <button data-modal="modalAñadirPrestamo" id="btnAddPrestamo" style="display:none;">Nuevo Préstamo</button>
      <button data-modal="modalAñadirUsuario" id="btnAddUsuario" style="display:none;">Nuevo Usuario</button>
    </div>
  </div>

  <div id="modalEditarLibro" class="modal">
    <div class="modal-content">
      <h2>Editar Libro</h2>
      <input type="hidden" id="editIdLibro">
      <label for="editTitulo">Título</label>
      <input type="text" id="editTitulo" placeholder="Título">
      <label for="editAutor">Autor</label>
      <input type="text" id="editAutor" placeholder="Autor">
      <label for="editIsbn">ISBN</label>
      <input type="text" id="editIsbn" placeholder="ISBN">
      <label for="editCantidad">Cantidad total</label>
      <input type="number" id="editCantidad" placeholder="Cantidad total">
      <label for="editDisponibles">Cantidad disponibles</label>
      <input type="number" id="editDisponibles" placeholder="Cantidad disponibles">
      <label for="editImagenUrl">URL Imagen</label>
      <input type="text" id="editImagenUrl" placeholder="URL Imagen">
      <label for="editEstado">Estado</label>
      <select id="editEstado">
        <option value="fisico">Físico</option>
        <option value="digital">Digital</option>
        <option value="ambos">Ambos</option>
      </select>
      <button class="primary" onclick="guardarEdicionLibro()">Guardar Cambios</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalAñadirLibro" class="modal">
    <div class="modal-content">
      <h2>Añadir Libro</h2>
      <label for="addTitulo">Título</label>
      <input type="text" id="addTitulo" placeholder="Título">
      <label for="addAutor">Autor</label>
      <input type="text" id="addAutor" placeholder="Autor">
      <label for="addIsbn">ISBN</label>
      <input type="text" id="addIsbn" placeholder="ISBN">
      <label for="addCantidad">Cantidad total</label>
      <input type="number" id="addCantidad" placeholder="Cantidad total" min="1" value="1">
      <label for="addDisponibles">Cantidad disponibles</label>
      <input type="number" id="addDisponibles" placeholder="Cantidad disponibles" min="0" value="1">
      <label for="addImagenUrl">URL Imagen</label>
      <input type="text" id="addImagenUrl" placeholder="URL Imagen">
      <label for="addEstado">Estado</label>
      <select id="addEstado">
        <option value="fisico">Físico</option>
        <option value="digital">Digital</option>
        <option value="ambos">Ambos</option>
      </select>
      <button class="primary" onclick="agregarLibro()">Agregar Libro</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalEliminarLibro" class="modal">
    <div class="modal-content">
      <h2>Eliminar Libro</h2>
      <p>¿Está seguro que desea eliminar este libro?</p>
      <input type="hidden" id="deleteIdLibro">
      <button class="primary danger" onclick="eliminarLibro()">Eliminar</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalEditarAutor" class="modal">
    <div class="modal-content">
      <h2>Editar Autor</h2>
      <input type="hidden" id="editIdAutor">
      <label for="editNombreAutor">Nombre</label>
      <input type="text" id="editNombreAutor" placeholder="Nombre">
      <label for="editNacionalidad">Nacionalidad</label>
      <input type="text" id="editNacionalidad" placeholder="Nacionalidad">
      <label for="editFechaNacimiento">Fecha Nacimiento</label>
      <input type="date" id="editFechaNacimiento">
      <button class="primary" onclick="guardarEdicionAutor()">Guardar Cambios</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalAñadirAutor" class="modal">
    <div class="modal-content">
      <h2>Añadir Autor</h2>
      <label for="addNombreAutor">Nombre</label>
      <input type="text" id="addNombreAutor" placeholder="Nombre">
      <label for="addNacionalidad">Nacionalidad</label>
      <input type="text" id="addNacionalidad" placeholder="Nacionalidad">
      <label for="addFechaNacimiento">Fecha Nacimiento</label>
      <input type="date" id="addFechaNacimiento">
      <button class="primary" onclick="agregarAutor()">Agregar Autor</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalEliminarAutor" class="modal">
    <div class="modal-content">
      <h2>Eliminar Autor</h2>
      <p>¿Está seguro que desea eliminar este autor?</p>
      <input type="hidden" id="deleteIdAutor">
      <button class="primary danger" onclick="eliminarAutor()">Eliminar</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalEditarPrestamo" class="modal">
    <div class="modal-content">
      <h2>Editar Préstamo</h2>
      <input type="hidden" id="editIdPrestamo">
      <label for="editUsuarioIdPrestamo">ID Usuario</label>
      <input type="number" id="editUsuarioIdPrestamo" placeholder="ID Usuario">
      <label for="editLibroIdPrestamo">ID Libro</label>
      <input type="number" id="editLibroIdPrestamo" placeholder="ID Libro">
      <label for="editFechaDevolucionPrestamo">Fecha Devolución Estimada</label>
      <input type="date" id="editFechaDevolucionPrestamo">
      <label for="editEstadoPrestamo">Estado</label>
      <select id="editEstadoPrestamo">
        <option value="activo">Activo</option>
        <option value="devuelto">Devuelto</option>
        <option value="retrasado">Retrasado</option>
      </select>
      <button class="primary" onclick="guardarEdicionPrestamo()">Guardar Cambios</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalAñadirPrestamo" class="modal">
    <div class="modal-content">
      <h2>Nuevo Préstamo</h2>
      <label for="addUsuarioIdPrestamo">ID Usuario</label>
      <input type="number" id="addUsuarioIdPrestamo" placeholder="ID Usuario">
      <label for="addLibroIdPrestamo">ID Libro</label>
      <input type="number" id="addLibroIdPrestamo" placeholder="ID Libro">
      <label for="addFechaDevolucionPrestamo">Fecha Devolución Estimada</label>
      <input type="date" id="addFechaDevolucionPrestamo">
      <button class="primary" onclick="agregarPrestamo()">Registrar Préstamo</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalEliminarPrestamo" class="modal">
    <div class="modal-content">
      <h2>Eliminar Préstamo</h2>
      <p>¿Está seguro que desea eliminar este préstamo?</p>
      <input type="hidden" id="deleteIdPrestamo">
      <button class="primary danger" onclick="eliminarPrestamo()">Eliminar</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalRegistrarDevolucion" class="modal">
    <div class="modal-content">
      <h2>Registrar Devolución</h2>
      <p>¿Desea registrar la devolución de este préstamo?</p>
      <input type="hidden" id="devolucionIdPrestamo">
      <button class="primary" onclick="registrarDevolucion()">Registrar Devolución</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalEditarUsuario" class="modal">
    <div class="modal-content">
      <h2>Editar Usuario</h2>
      <input type="hidden" id="editIdUsuario">
      <label for="editNombreUsuario">Nombre</label>
      <input type="text" id="editNombreUsuario" placeholder="Nombre">
      <label for="editEmailUsuario">Email</label>
      <input type="email" id="editEmailUsuario" placeholder="Email">
      <label for="editPasswordUsuario">Nueva Contraseña (dejar vacío para no cambiar)</label>
      <input type="password" id="editPasswordUsuario" placeholder="Contraseña">
      <label for="editRolUsuario">Rol</label>
      <select id="editRolUsuario">
        <option value="usuario">Usuario</option>
        <option value="admin">Admin</option>
      </select>
      <button class="primary" onclick="guardarEdicionUsuario()">Guardar Cambios</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalAñadirUsuario" class="modal">
    <div class="modal-content">
      <h2>Nuevo Usuario</h2>
      <label for="addNombreUsuario">Nombre</label>
      <input type="text" id="addNombreUsuario" placeholder="Nombre">
      <label for="addEmailUsuario">Email</label>
      <input type="email" id="addEmailUsuario" placeholder="Email">
      <label for="addPasswordUsuario">Contraseña</label>
      <input type="password" id="addPasswordUsuario" placeholder="Contraseña">
      <label for="addRolUsuario">Rol</label>
      <select id="addRolUsuario">
        <option value="usuario">Usuario</option>
        <option value="admin">Admin</option>
      </select>
      <button class="primary" onclick="agregarUsuario()">Agregar Usuario</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <div id="modalEliminarUsuario" class="modal">
    <div class="modal-content">
      <h2>Eliminar Usuario</h2>
      <p>¿Está seguro que desea eliminar este usuario?</p>
      <input type="hidden" id="deleteIdUsuario">
      <button class="primary danger" onclick="eliminarUsuario()">Eliminar</button>
      <button class="close">Cerrar</button>
    </div>
  </div>

  <script src="../../assets/js/index.js"></script>
<script>
let pestanaActual = 'libros';
let datosOriginales = {
  libros: [],
  autores: [],
  prestamos: [],
  devoluciones: [],
  usuarios: []
};

function cambiarPestana(pestana) {
  pestanaActual = pestana;
  
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelector(`[data-tab="${pestana}"]`).classList.add('active');
  
  document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
  document.getElementById(`tabla${pestana.charAt(0).toUpperCase() + pestana.slice(1)}`).classList.add('active');
  
  const titulos = {
    libros: 'Catálogo de Libros',
    autores: 'Autores',
    prestamos: 'Préstamos',
    devoluciones: 'Devoluciones',
    usuarios: 'Usuarios'
  };
  document.getElementById('sectionTitle').textContent = titulos[pestana];
  
  document.getElementById('btnAddLibro').style.display = pestana === 'libros' ? 'block' : 'none';
  document.getElementById('btnAddAutor').style.display = pestana === 'autores' ? 'block' : 'none';
  document.getElementById('btnAddPrestamo').style.display = pestana === 'prestamos' ? 'block' : 'none';
  document.getElementById('btnAddUsuario').style.display = pestana === 'usuarios' ? 'block' : 'none';
  
  cargarDatos();
  document.getElementById('searchBar').value = '';
}

function buscar() {
  const termino = document.getElementById('searchBar').value.toLowerCase();
  let datos = datosOriginales[pestanaActual] || [];
  
  if (!termino) {
    renderizarDatos(datos);
    return;
  }
  
  let filtrados = [];
  switch(pestanaActual) {
    case 'libros':
      filtrados = datos.filter(item => 
        item.titulo.toLowerCase().includes(termino) ||
        item.autor.toLowerCase().includes(termino) ||
        (item.isbn && item.isbn.toLowerCase().includes(termino))
      );
      break;
    case 'autores':
      filtrados = datos.filter(item => 
        item.nombre.toLowerCase().includes(termino) ||
        (item.nacionalidad && item.nacionalidad.toLowerCase().includes(termino))
      );
      break;
    case 'prestamos':
      filtrados = datos.filter(item => 
        item.usuario.toLowerCase().includes(termino) ||
        item.libro.toLowerCase().includes(termino)
      );
      break;
    case 'devoluciones':
      filtrados = datos.filter(item => 
        item.usuario.toLowerCase().includes(termino) ||
        item.libro.toLowerCase().includes(termino)
      );
      break;
    case 'usuarios':
      filtrados = datos.filter(item => 
        item.nombre.toLowerCase().includes(termino) ||
        item.email.toLowerCase().includes(termino)
      );
      break;
  }
  
  renderizarDatos(filtrados);
}

function cargarDatos() {
  switch(pestanaActual) {
    case 'libros':
      cargarLibros();
      break;
    case 'autores':
      cargarAutores();
      break;
    case 'prestamos':
      cargarPrestamos();
      break;
    case 'devoluciones':
      cargarDevoluciones();
      break;
    case 'usuarios':
      cargarUsuarios();
      break;
  }
}

function renderizarDatos(datos) {
  switch(pestanaActual) {
    case 'libros':
      renderizarLibros(datos);
      break;
    case 'autores':
      renderizarAutores(datos);
      break;
    case 'prestamos':
      renderizarPrestamos(datos);
      break;
    case 'devoluciones':
      renderizarDevoluciones(datos);
      break;
    case 'usuarios':
      renderizarUsuarios(datos);
      break;
  }
}

function cargarLibros() {
    fetch(`${window.location.origin}/api/libros.php`)
        .then(res => res.json())
        .then(libros => {
      datosOriginales.libros = libros;
      renderizarLibros(libros);
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('tbodyLibros').innerHTML = '<tr><td colspan="8" class="error-message">Error al cargar los libros</td></tr>';
    });
}

function renderizarLibros(libros) {
  let tbody = document.getElementById('tbodyLibros');
            tbody.innerHTML = '';
            libros.forEach(libro => {
                let tr = document.createElement("tr");
                const prestado = (libro.cantidad && libro.disponibles) ? (libro.cantidad - libro.disponibles) : 0;
    const estadoTexto = libro.estado.charAt(0).toUpperCase() + libro.estado.slice(1);
                tr.innerHTML = `
                  <td class="id-cell">${libro.id}</td>
                  <td class="title-cell">${libro.titulo}</td>
                  <td class="author-cell">${libro.autor}</td>
      <td>${libro.isbn || '-'}</td>
                  <td>${prestado}</td>
                  <td>${libro.disponibles ?? 0}</td>
      <td><span class="status-badge">${estadoTexto}</span></td>
                  <td class="actions-cell">
        <button class="action-btn edit-btn" title="Editar" onclick="abrirEditarLibro(${libro.id}, '${libro.titulo.replace(/'/g, "\\'")}', '${libro.autor.replace(/'/g, "\\'")}', ${libro.cantidad ?? 1}, ${libro.disponibles ?? 0}, '${libro.estado}', '${(libro.isbn || '').replace(/'/g, "\\'")}', '${(libro.imagen_url || '').replace(/'/g, "\\'")}')">✏️</button>
        <button class="action-btn delete-btn" title="Eliminar" onclick="abrirEliminarLibro(${libro.id})">🗑️</button>
                  </td>
                `;
                tbody.appendChild(tr);
  });
}

function abrirEditarLibro(id, titulo, autor, cantidad, disponibles, estado, isbn, imagenUrl) {
  document.getElementById('editIdLibro').value = id;
  document.getElementById('editTitulo').value = titulo;
  document.getElementById('editAutor').value = autor;
  document.getElementById('editCantidad').value = cantidad;
  document.getElementById('editDisponibles').value = disponibles;
  document.getElementById('editEstado').value = estado;
  document.getElementById('editIsbn').value = isbn || '';
  document.getElementById('editImagenUrl').value = imagenUrl || '';
  document.getElementById('modalEditarLibro').classList.add('active');
}

function guardarEdicionLibro() {
  const id = document.getElementById('editIdLibro').value;
  const titulo = document.getElementById('editTitulo').value;
  const autor = document.getElementById('editAutor').value;
  const cantidad = document.getElementById('editCantidad').value;
  const disponibles = document.getElementById('editDisponibles').value;
  const estado = document.getElementById('editEstado').value;
  const isbn = document.getElementById('editIsbn').value;
  const imagenUrl = document.getElementById('editImagenUrl').value;

  if(!titulo || !autor || cantidad === '' || disponibles === '') {
    alert('Por favor completa todos los campos obligatorios');
    return;
  }

  fetch(`${window.location.origin}/api/libros.php`, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id, titulo, autor, cantidad, disponibles, estado, isbn, imagen_url: imagenUrl })
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalEditarLibro').classList.remove('active');
      cargarLibros();
    } else {
      alert(data.error || 'Error al editar el libro');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al editar el libro');
  });
}

function abrirEliminarLibro(id) {
  document.getElementById('deleteIdLibro').value = id;
  document.getElementById('modalEliminarLibro').classList.add('active');
}

function eliminarLibro() {
  const id = document.getElementById('deleteIdLibro').value;
    if(!id) {
        alert('Por favor ingresa el ID del libro');
        return;
    }

  fetch(`${window.location.origin}/api/libros.php?id=${id}`, {method: 'DELETE'})
    .then(res => res.json())
    .then(data => {
        if(data.status) {
            alert(data.status);
        document.getElementById('modalEliminarLibro').classList.remove('active');
            cargarLibros();
        } else {
            alert(data.error || 'Error al eliminar el libro');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el libro');
    });
}

function agregarLibro() {
    const titulo = document.getElementById('addTitulo').value;
    const autor = document.getElementById('addAutor').value;
    const cantidad = document.getElementById('addCantidad').value;
    const disponibles = document.getElementById('addDisponibles').value;
    const estado = document.getElementById('addEstado').value;
  const isbn = document.getElementById('addIsbn').value;
  const imagenUrl = document.getElementById('addImagenUrl').value;

    if(!titulo || !autor || cantidad === '' || disponibles === '' || estado === '') {
    alert('Por favor completa todos los campos obligatorios');
        return;
    }

    fetch(`${window.location.origin}/api/libros.php`, {
        method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ titulo, autor, cantidad, disponibles, estado, isbn, imagen_url: imagenUrl })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status) {
            alert(data.status);
      document.getElementById('modalAñadirLibro').classList.remove('active');
            document.getElementById('addTitulo').value = '';
            document.getElementById('addAutor').value = '';
            document.getElementById('addCantidad').value = 1;
            document.getElementById('addDisponibles').value = 1;
            document.getElementById('addEstado').value = 'fisico';
      document.getElementById('addIsbn').value = '';
      document.getElementById('addImagenUrl').value = '';
            cargarLibros();
        } else {
            alert(data.error || 'Error al agregar el libro');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar el libro');
    });
}

function cargarAutores() {
  fetch(`${window.location.origin}/api/autores.php`)
    .then(res => res.json())
    .then(autores => {
      datosOriginales.autores = autores;
      renderizarAutores(autores);
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('tbodyAutores').innerHTML = '<tr><td colspan="5" class="error-message">Error al cargar los autores</td></tr>';
    });
}

function renderizarAutores(autores) {
  let tbody = document.getElementById('tbodyAutores');
  tbody.innerHTML = '';
  autores.forEach(autor => {
    let tr = document.createElement("tr");
    tr.innerHTML = `
      <td class="id-cell">${autor.id}</td>
      <td>${autor.nombre}</td>
      <td>${autor.nacionalidad || '-'}</td>
      <td>${autor.fecha_nacimiento || '-'}</td>
      <td class="actions-cell">
        <button class="action-btn edit-btn" title="Editar" onclick="abrirEditarAutor(${autor.id}, '${autor.nombre.replace(/'/g, "\\'")}', '${(autor.nacionalidad || '').replace(/'/g, "\\'")}', '${autor.fecha_nacimiento || ''}')">✏️</button>
        <button class="action-btn delete-btn" title="Eliminar" onclick="abrirEliminarAutor(${autor.id})">🗑️</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function abrirEditarAutor(id, nombre, nacionalidad, fechaNacimiento) {
  document.getElementById('editIdAutor').value = id;
  document.getElementById('editNombreAutor').value = nombre;
  document.getElementById('editNacionalidad').value = nacionalidad || '';
  document.getElementById('editFechaNacimiento').value = fechaNacimiento || '';
  document.getElementById('modalEditarAutor').classList.add('active');
}

function guardarEdicionAutor() {
  const id = document.getElementById('editIdAutor').value;
  const nombre = document.getElementById('editNombreAutor').value;
  const nacionalidad = document.getElementById('editNacionalidad').value;
  const fechaNacimiento = document.getElementById('editFechaNacimiento').value;

  if(!nombre) {
    alert('Por favor completa el nombre');
    return;
  }

  fetch(`${window.location.origin}/api/autores.php`, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id, nombre, nacionalidad, fecha_nacimiento: fechaNacimiento })
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalEditarAutor').classList.remove('active');
      cargarAutores();
    } else {
      alert(data.error || 'Error al editar el autor');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al editar el autor');
  });
}

function abrirEliminarAutor(id) {
  document.getElementById('deleteIdAutor').value = id;
  document.getElementById('modalEliminarAutor').classList.add('active');
}

function eliminarAutor() {
  const id = document.getElementById('deleteIdAutor').value;
  if(!id) return;

  fetch(`${window.location.origin}/api/autores.php?id=${id}`, {method: 'DELETE'})
    .then(res => res.json())
    .then(data => {
      if(data.status) {
        alert(data.status);
        document.getElementById('modalEliminarAutor').classList.remove('active');
        cargarAutores();
      } else {
        alert(data.error || 'Error al eliminar el autor');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al eliminar el autor');
    });
}

function agregarAutor() {
  const nombre = document.getElementById('addNombreAutor').value;
  const nacionalidad = document.getElementById('addNacionalidad').value;
  const fechaNacimiento = document.getElementById('addFechaNacimiento').value;

  if(!nombre) {
    alert('Por favor completa el nombre');
    return;
  }

  fetch(`${window.location.origin}/api/autores.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ nombre, nacionalidad, fecha_nacimiento: fechaNacimiento })
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalAñadirAutor').classList.remove('active');
      document.getElementById('addNombreAutor').value = '';
      document.getElementById('addNacionalidad').value = '';
      document.getElementById('addFechaNacimiento').value = '';
      cargarAutores();
    } else {
      alert(data.error || 'Error al agregar el autor');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al agregar el autor');
  });
}

function cargarPrestamos() {
  fetch(`${window.location.origin}/api/prestamos.php`)
    .then(res => res.json())
    .then(prestamos => {
      datosOriginales.prestamos = prestamos;
      renderizarPrestamos(prestamos);
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('tbodyPrestamos').innerHTML = '<tr><td colspan="7" class="error-message">Error al cargar los prestamos</td></tr>';
    });
}

function renderizarPrestamos(prestamos) {
  let tbody = document.getElementById('tbodyPrestamos');
  tbody.innerHTML = '';
  prestamos.forEach(prestamo => {
    let tr = document.createElement("tr");
    const estadoTexto = prestamo.Estado ? prestamo.Estado.charAt(0).toUpperCase() + prestamo.Estado.slice(1) : 'Activo';
    tr.innerHTML = `
      <td class="id-cell">${prestamo.id}</td>
      <td>${prestamo.usuario}</td>
      <td>${prestamo.libro}</td>
      <td>${prestamo.fecha_prestamo}</td>
      <td>${prestamo.fecha_devolucion || '-'}</td>
      <td><span class="status-badge">${estadoTexto}</span></td>
      <td class="actions-cell">
        <button class="action-btn edit-btn" title="Editar" onclick="abrirEditarPrestamo(${prestamo.id}, ${prestamo.usuario_id}, ${prestamo.libro_id}, '${prestamo.fecha_devolucion || ''}', '${prestamo.Estado || 'activo'}')">✏️</button>
        <button class="action-btn delete-btn" title="Eliminar" onclick="abrirEliminarPrestamo(${prestamo.id})">🗑️</button>
        ${prestamo.Estado === 'activo' ? `<button class="action-btn" title="Devolver" onclick="abrirDevolucion(${prestamo.id})" style="background-color: #4CAF50; color: white; padding: 6px 10px; border-radius: 6px; border: none; cursor: pointer;">↩️</button>` : ''}
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function abrirEditarPrestamo(id, usuarioId, libroId, fechaDevolucion, estado) {
  document.getElementById('editIdPrestamo').value = id;
  document.getElementById('editUsuarioIdPrestamo').value = usuarioId;
  document.getElementById('editLibroIdPrestamo').value = libroId;
  document.getElementById('editFechaDevolucionPrestamo').value = fechaDevolucion || '';
  document.getElementById('editEstadoPrestamo').value = estado || 'activo';
  document.getElementById('modalEditarPrestamo').classList.add('active');
}

function guardarEdicionPrestamo() {
  const id = document.getElementById('editIdPrestamo').value;
  const usuarioId = document.getElementById('editUsuarioIdPrestamo').value;
  const libroId = document.getElementById('editLibroIdPrestamo').value;
  const fechaDevolucion = document.getElementById('editFechaDevolucionPrestamo').value;
  const estado = document.getElementById('editEstadoPrestamo').value;

  if(!usuarioId || !libroId) {
    alert('Por favor completa todos los campos');
    return;
  }

  fetch(`${window.location.origin}/api/prestamos.php`, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id, usuario_id: usuarioId, libro_id: libroId, fecha_limite: fechaDevolucion, estado })
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalEditarPrestamo').classList.remove('active');
      cargarPrestamos();
    } else {
      alert(data.error || 'Error al editar el prestamo');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al editar el prestamo');
  });
}

function abrirEliminarPrestamo(id) {
  document.getElementById('deleteIdPrestamo').value = id;
  document.getElementById('modalEliminarPrestamo').classList.add('active');
}

function eliminarPrestamo() {
  const id = document.getElementById('deleteIdPrestamo').value;
  if(!id) return;

  fetch(`${window.location.origin}/api/prestamos.php?id=${id}`, {method: 'DELETE'})
    .then(res => res.json())
    .then(data => {
      if(data.status) {
        alert(data.status);
        document.getElementById('modalEliminarPrestamo').classList.remove('active');
        cargarPrestamos();
      } else {
        alert(data.error || 'Error al eliminar el prestamo');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al eliminar el prestamo');
    });
}

function agregarPrestamo() {
  const usuarioId = document.getElementById('addUsuarioIdPrestamo').value;
  const libroId = document.getElementById('addLibroIdPrestamo').value;
  const fechaDevolucion = document.getElementById('addFechaDevolucionPrestamo').value;

  if(!usuarioId || !libroId) {
    alert('Por favor completa todos los campos');
    return;
  }

  fetch(`${window.location.origin}/api/prestamos.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ usuario_id: usuarioId, libro_id: libroId, fecha_limite: fechaDevolucion })
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalAñadirPrestamo').classList.remove('active');
      document.getElementById('addUsuarioIdPrestamo').value = '';
      document.getElementById('addLibroIdPrestamo').value = '';
      document.getElementById('addFechaDevolucionPrestamo').value = '';
      cargarPrestamos();
    } else {
      alert(data.error || 'Error al agregar el prestamo');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al agregar el prestamo');
  });
}

function abrirDevolucion(id) {
  document.getElementById('devolucionIdPrestamo').value = id;
  document.getElementById('modalRegistrarDevolucion').classList.add('active');
}

function registrarDevolucion() {
  const id = document.getElementById('devolucionIdPrestamo').value;
  if(!id) return;

  fetch(`${window.location.origin}/api/devoluciones.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ prestamo_id: id })
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalRegistrarDevolucion').classList.remove('active');
      cargarPrestamos();
      if(pestanaActual === 'devoluciones') cargarDevoluciones();
    } else {
      alert(data.error || 'Error al registrar la devolucion');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al registrar la devolucion');
  });
}

function cargarDevoluciones() {
  fetch(`${window.location.origin}/api/devoluciones.php`)
    .then(res => res.json())
    .then(devoluciones => {
      datosOriginales.devoluciones = devoluciones;
      renderizarDevoluciones(devoluciones);
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('tbodyDevoluciones').innerHTML = '<tr><td colspan="6" class="error-message">Error al cargar las devoluciones</td></tr>';
    });
}

function renderizarDevoluciones(devoluciones) {
  let tbody = document.getElementById('tbodyDevoluciones');
  tbody.innerHTML = '';
  devoluciones.forEach(devolucion => {
    let tr = document.createElement("tr");
    tr.innerHTML = `
      <td class="id-cell">${devolucion.id}</td>
      <td>${devolucion.usuario}</td>
      <td>${devolucion.libro}</td>
      <td>${devolucion.fecha_prestamo}</td>
      <td>${devolucion.fecha_devolucion_real || '-'}</td>
      <td class="actions-cell">
        <span class="status-badge status-available">Devuelto</span>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function cargarUsuarios() {
  fetch(`${window.location.origin}/api/usuarios.php`)
    .then(res => res.json())
    .then(usuarios => {
      datosOriginales.usuarios = usuarios;
      renderizarUsuarios(usuarios);
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('tbodyUsuarios').innerHTML = '<tr><td colspan="6" class="error-message">Error al cargar los usuarios</td></tr>';
    });
}

function renderizarUsuarios(usuarios) {
  let tbody = document.getElementById('tbodyUsuarios');
  tbody.innerHTML = '';
  usuarios.forEach(usuario => {
    let tr = document.createElement("tr");
    const rolTexto = usuario.rol ? usuario.rol.charAt(0).toUpperCase() + usuario.rol.slice(1) : 'Usuario';
    tr.innerHTML = `
      <td class="id-cell">${usuario.id}</td>
      <td>${usuario.nombre}</td>
      <td>${usuario.email}</td>
      <td><span class="status-badge">${rolTexto}</span></td>
      <td>${usuario.fecha_registro ? usuario.fecha_registro.split(' ')[0] : '-'}</td>
      <td class="actions-cell">
        <button class="action-btn edit-btn" title="Editar" onclick="abrirEditarUsuario(${usuario.id}, '${usuario.nombre.replace(/'/g, "\\'")}', '${usuario.email.replace(/'/g, "\\'")}', '${usuario.rol || 'usuario'}')">✏️</button>
        <button class="action-btn delete-btn" title="Eliminar" onclick="abrirEliminarUsuario(${usuario.id})">🗑️</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function abrirEditarUsuario(id, nombre, email, rol) {
  document.getElementById('editIdUsuario').value = id;
  document.getElementById('editNombreUsuario').value = nombre;
  document.getElementById('editEmailUsuario').value = email;
  document.getElementById('editRolUsuario').value = rol;
  document.getElementById('editPasswordUsuario').value = '';
  document.getElementById('modalEditarUsuario').classList.add('active');
}

function guardarEdicionUsuario() {
  const id = document.getElementById('editIdUsuario').value;
  const nombre = document.getElementById('editNombreUsuario').value;
  const email = document.getElementById('editEmailUsuario').value;
  const password = document.getElementById('editPasswordUsuario').value;
  const rol = document.getElementById('editRolUsuario').value;

  if(!nombre || !email) {
    alert('Por favor completa todos los campos obligatorios');
    return;
  }

  const datos = { id, nombre, email, rol };
  if(password) datos.password = password;

  fetch(`${window.location.origin}/api/usuarios.php`, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(datos)
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalEditarUsuario').classList.remove('active');
      cargarUsuarios();
    } else {
      alert(data.error || 'Error al editar el usuario');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al editar el usuario');
  });
}

function abrirEliminarUsuario(id) {
  document.getElementById('deleteIdUsuario').value = id;
  document.getElementById('modalEliminarUsuario').classList.add('active');
}

function eliminarUsuario() {
  const id = document.getElementById('deleteIdUsuario').value;
  if(!id) return;

  fetch(`${window.location.origin}/api/usuarios.php?id=${id}`, {method: 'DELETE'})
    .then(res => res.json())
    .then(data => {
      if(data.status) {
        alert(data.status);
        document.getElementById('modalEliminarUsuario').classList.remove('active');
        cargarUsuarios();
      } else {
        alert(data.error || 'Error al eliminar el usuario');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error al eliminar el usuario');
    });
}

function agregarUsuario() {
  const nombre = document.getElementById('addNombreUsuario').value;
  const email = document.getElementById('addEmailUsuario').value;
  const password = document.getElementById('addPasswordUsuario').value;
  const rol = document.getElementById('addRolUsuario').value;

  if(!nombre || !email || !password) {
    alert('Por favor completa todos los campos');
    return;
  }

  fetch(`${window.location.origin}/api/usuarios.php`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ nombre, email, password, rol })
  })
  .then(res => res.json())
  .then(data => {
    if(data.status) {
      alert(data.status);
      document.getElementById('modalAñadirUsuario').classList.remove('active');
      document.getElementById('addNombreUsuario').value = '';
      document.getElementById('addEmailUsuario').value = '';
      document.getElementById('addPasswordUsuario').value = '';
      document.getElementById('addRolUsuario').value = 'usuario';
      cargarUsuarios();
    } else {
      alert(data.error || 'Error al agregar el usuario');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al agregar el usuario');
  });
}

document.addEventListener('DOMContentLoaded', function() {
  cargarLibros();
  
  document.querySelectorAll(".sidebar button").forEach(btn => {
    btn.addEventListener("click", e => {
      const modalID = btn.dataset.modal;
      if(modalID) {
        document.getElementById(modalID).classList.add("active");
      }
    });
  });

  document.querySelectorAll(".modal .close").forEach(btn => {
    btn.addEventListener("click", () => {
      btn.closest(".modal").classList.remove("active");
    });
  });

  document.querySelectorAll(".modal").forEach(modal => {
    modal.addEventListener("click", e => {
      if (e.target === modal) modal.classList.remove("active");
    });
  });
});
</script>

</body>
</html>

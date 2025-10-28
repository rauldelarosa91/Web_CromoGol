console.log('Archivo carrito.js cargado');

document.addEventListener('DOMContentLoaded', () => {
    cargarCarrito(); // Cargar el carrito al cargar la página
    actualizarContadorCarrito();

    const botonesAddToCart = document.querySelectorAll('.add-to-cart-btn');
    botonesAddToCart.forEach(boton => {
        boton.addEventListener('click', function() {
            const nombre = this.dataset.nombre;
            const precio = parseFloat(this.dataset.precio);
            const referencia = this.dataset.referencia;
            const nuevaCarta = { referencia, nombre, precio, cantidad: 1 };
            agregarAlCarrito(nuevaCarta);
            actualizarContadorCarrito();
        });
    });
});

let carrito = [];

function cargarCarrito() {
    const carritoGuardado = localStorage.getItem('carrito');
    if (carritoGuardado) {
        carrito = JSON.parse(carritoGuardado);
    }
}

function guardarCarrito() {
    localStorage.setItem('carrito', JSON.stringify(carrito));
}

function agregarAlCarrito(carta) {
    const index = carrito.findIndex(item => item.referencia === carta.referencia);
    if (index !== -1) {
        carrito[index].cantidad++;
    } else {
        carrito.push(carta);
    }
    guardarCarrito();
    console.log('Carrito actualizado:', carrito);
}

function actualizarContadorCarrito() {
    const contadorCarrito = document.getElementById('contador-carrito');
    if (contadorCarrito) {
        const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
        contadorCarrito.textContent = totalItems;
    }
}

// Funciones para la página del carrito (carrito.php)

function mostrarCarritoEnPagina() {
    const listaCarrito = document.getElementById('lista-carrito');
    const totalCarritoElement = document.getElementById('total-carrito');

    if (listaCarrito && totalCarritoElement) {
        listaCarrito.innerHTML = '';
        let total = 0;

        carrito.forEach(item => {
            const listItem = document.createElement('li');
            listItem.innerHTML = `
                ${item.nombre} - Precio: ${item.precio} € - Cantidad:
                <button class="btn-menos" data-referencia="${item.referencia}">-</button>
                <span>${item.cantidad}</span>
                <button class="btn-mas" data-referencia="${item.referencia}">+</button>
                Subtotal: ${(item.precio * item.cantidad).toFixed(2)} €
                <button class="btn-eliminar" data-referencia="${item.referencia}">Eliminar</button>
            `;
            listaCarrito.appendChild(listItem);
            total += item.precio * item.cantidad;
        });

        totalCarritoElement.textContent = total.toFixed(2);

        // Añadir event listeners para los botones de cantidad y eliminar
        const botonesMenos = document.querySelectorAll('.btn-menos');
        botonesMenos.forEach(boton => {
            boton.addEventListener('click', function() {
                const referencia = this.dataset.referencia;
                cambiarCantidad(referencia, -1);
                mostrarCarritoEnPagina();
                actualizarContadorCarrito();
            });
        });

        const botonesMas = document.querySelectorAll('.btn-mas');
        botonesMas.forEach(boton => {
            boton.addEventListener('click', function() {
                const referencia = this.dataset.referencia;
                cambiarCantidad(referencia, 1);
                mostrarCarritoEnPagina();
                actualizarContadorCarrito();
            });
        });

        const botonesEliminar = document.querySelectorAll('.btn-eliminar');
        botonesEliminar.forEach(boton => {
            boton.addEventListener('click', function() {
                const referencia = this.dataset.referencia;
                eliminarDelCarrito(referencia);
                mostrarCarritoEnPagina();
                actualizarContadorCarrito();
            });
        });

    }
}

function cambiarCantidad(referencia, cambio) {
    const index = carrito.findIndex(item => item.referencia === referencia);
    if (index !== -1) {
        carrito[index].cantidad += cambio;
        if (carrito[index].cantidad < 1) {
            eliminarDelCarrito(referencia);
        }
        guardarCarrito();
    }
}

function eliminarDelCarrito(referencia) {
    carrito = carrito.filter(item => item.referencia !== referencia);
    guardarCarrito();
}

// Ejecutar mostrarCarritoEnPagina si estamos en la página del carrito
if (document.URL.includes('carrito.php')) {
    document.addEventListener('DOMContentLoaded', mostrarCarritoEnPagina);
}
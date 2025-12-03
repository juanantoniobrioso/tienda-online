const API_URL = '/tienda-online/api/';

const App = {

    init: () => {
        const path = window.location.pathname;
        const page = path.split("/").pop();

        if (page !== 'login.html') {
            App.checkAuth();
            App.updateNav();
        }
        
        // Router 
        if (page === 'dashboard.html') App.renderDashboard();
        if (page === 'categories.html') App.renderCategories();
        if (page === 'product.html') App.renderProductDetail();
        if (page === 'cart.html') App.renderCart();
    },

    checkAuth: () => {
        const token = localStorage.getItem('token');
        if (!token) window.location.href = 'login.html';
    },

    login: async (usuario, password) => {
        const res = await fetch(API_URL + 'login.php', {
            method: 'POST',
            body: JSON.stringify({ usuario, password })
        });
        const data = await res.json();
        
        if (data.success) {
            localStorage.setItem('token', data.token);
            // Guardamos toda la tienda en local
            localStorage.setItem('storeData', JSON.stringify(data.data));
            window.location.href = 'dashboard.html';
        } else {
            alert(data.message);
        }
    },

    logout: () => {
        localStorage.clear();
        window.location.href = 'login.html';
    },

    // Los datos locales
    getStore: () => JSON.parse(localStorage.getItem('storeData')),
    
    addToCart: (productoId) => {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const store = App.getStore();
        const prod = store.productos.find(p => p.id == productoId);
        
        const existe = cart.find(item => item.id == productoId);
        if (existe) {
            existe.cantidad++;
        } else {
            cart.push({ ...prod, cantidad: 1 });
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        alert('Producto añadido');
    },

    saveRecent: (prod) => {
        // Para que se muestre la lista
        let recent = JSON.parse(localStorage.getItem('productos_vistos')) || [];
        
        // Evitar duplicado
        recent = recent.filter(p => p.id !== prod.id);
        
        // Añadirlo siempre al principio (por ejemplo si ves primero un movil y luego
        // un portatil, sale primero el portatil porque es lo último pero si vuelves a ver
        // el movil, vuelve a salir el movil el primero.)
        recent.unshift(prod);
        
        // Mantenemos solo los últimos 5 
        if (recent.length > 5) recent.pop();
        
        // Guardar en el navegador
        localStorage.setItem('productos_vistos', JSON.stringify(recent));

        // Enviar datos a productos_vistos.php
        const historialIds = recent.map(p => p.id);
        const token = localStorage.getItem('token');

        if(token) {
            fetch(API_URL + 'productos_vistos.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({ historial: historialIds })
            })
            .then(res => res.json())
            .then(data => console.log("Sync Servidor:", data.message))
            .catch(err => console.error("Error Sync:", err));
        }
    },

    // Renderizar
    renderDashboard: () => {
        const store = App.getStore();
        const container = document.getElementById('featured-products');
        const recentContainer = document.getElementById('recent-products');
        
        // Productos Destacados
        container.innerHTML = store.productos
            .filter(p => p.destacado)
            .map(p => App.createCard(p)).join('');

        // Vistos Recientemente
        const recent = JSON.parse(localStorage.getItem('productos_vistos')) || [];
        if(recent.length > 0) {
            recentContainer.innerHTML = recent.map(p => App.createCard(p)).join('');
        } else {
            recentContainer.innerHTML = '<p>No has visto productos aún.</p>';
        }
    },

    renderCategories: () => {
        const store = App.getStore();
        const list = document.getElementById('cat-list');
        const prodContainer = document.getElementById('cat-products');

        // Botones de categoría
        list.innerHTML = store.categorias.map(c => 
            `<button class="btn" style="width:auto; display:inline-block; margin-right:10px" 
              onclick="App.filterCategory(${c.id})">${c.nombre}</button>`
        ).join('');

        // Cargar todos inicialmente
        App.filterCategory = (idCat) => {
            prodContainer.innerHTML = store.productos
                .filter(p => p.id_categoria == idCat)
                .map(p => App.createCard(p)).join('');
        };
    },

    renderProductDetail: () => {
        const params = new URLSearchParams(window.location.search);
        const id = params.get('id');
        const store = App.getStore();
        
        const product = store.productos.find(p => p.id == id);

        if(product) {
            App.saveRecent(product); 
            
            const container = document.getElementById('detail');
            
            container.innerHTML = `
                <div class="detail-container">
                    
                    <div class="detail-img-col">
                        <img src="${product.img}" alt="${product.nombre}">
                    </div>

                    <div class="detail-info-col">
                        <h1 class="detail-title">${product.nombre}</h1>
                        <h2 class="detail-price">${product.precio} €</h2>
                        <p class="detail-description">
                            ${product.descripcion}
                        </p>
                        
                        <button class="btn btn-add-cart" onclick="App.addToCart(${product.id})">
                            Añadir al Carrito
                        </button> 
                        <br>
                    </div>
                </div>
            `;
        } else {
            document.getElementById('detail').innerHTML = "<h2>Producto no encontrado</h2>";
        }
    },

    renderCart: () => {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const container = document.getElementById('cart-items');
        let total = 0;

        if(cart.length === 0) {
            container.innerHTML = "<p>Carrito vacío</p>";
            return;
        }

        container.innerHTML = cart.map(item => {
            total += item.precio * item.cantidad;
            return `
                <div style="display:flex; justify-content:space-between; padding:10px; border-bottom:1px solid #333;">
                    <span>${item.nombre} (x${item.cantidad})</span>
                    <span>${item.precio * item.cantidad}€</span>
                </div>`;
        }).join('');
        
        document.getElementById('total-price').innerText = total;
    },

    checkout: async () => {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const token = localStorage.getItem('token');

        const res = await fetch(API_URL + 'carrito.php', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            body: JSON.stringify({ carrito: cart })
        });
        const data = await res.json();
        
        alert(data.message);
        if(data.success) {
            localStorage.removeItem('cart');
            window.location.reload();
        }
    },

    createCard: (p) => {
        return `
            <div class="card">
                <div>
                    <img src="${p.img}" alt="${p.nombre}">
                </div>
                
                <div class="card-body">
                    <h3>${p.nombre}</h3>
                    <p>${p.precio}€</p>
                    <a href="product.html?id=${p.id}" class="btn">Ver Detalle</a>
                    <button class="btn" onclick="App.addToCart(${p.id})">Añadir</button>
                </div>
            </div>
        `;
    },

    updateNav: () => {
        const user = localStorage.getItem('token');
    }
};

document.addEventListener('DOMContentLoaded', App.init);
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css" />
    <style>
        /* (Style kamu yang sudah bagus tetap dipakai tanpa perubahan) */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        .category-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 30px;
        }
        .category-btn {
            padding: 10px 15px;
            background-color: #f0f0f0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .category-btn:hover {
            background-color: #e0e0e0;
        }
        .category-btn.active {
            background-color: #007bff;
            color: white;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .product-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: none;
        }
        .product-card.active {
            display: block;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .product-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .product-description {
            color: #555;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        .product-id {
            font-size: 12px;
            color: #888;
            margin-top: 15px;
        }
        .loading {
            text-align: center;
            font-style: italic;
            color: #666;
            padding: 30px;
        }
        .error {
            color: #d32f2f;
            padding: 15px;
            background-color: #ffebee;
            border-radius: 5px;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        .timestamp {
            font-size: 12px;
            color: #888;
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        #error-container {
            display: none;
            margin-bottom: 20px;
            color: #d32f2f;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Daftar Produk</h1>

    <div id="error-container"></div>

    <div id="category-list" class="category-container">
        <p class="loading">Memuat kategori...</p>
    </div>

    <div id="product-list" class="product-container">
        <p class="loading">Memuat produk...</p>
    </div>

    <script type="module">
        import CategoryManager from '/js/categories.js';

        document.addEventListener('DOMContentLoaded', function () {
            const categoryListElement = document.getElementById('category-list');
            const productListElement = document.getElementById('product-list');
            const errorContainer = document.getElementById('error-container');
            let products = [];

            const categoryManager = new CategoryManager('http://localhost:8000/api');

            function showError(message) {
                errorContainer.textContent = message;
                errorContainer.style.display = 'block';
            }

            function hideError() {
                errorContainer.style.display = 'none';
            }

            function renderCategoryButtons(categories) {
                categoryListElement.innerHTML = '';

                if (!categories || categories.length === 0) {
                    categoryListElement.innerHTML = '<p>Tidak ada kategori yang tersedia.</p>';
                    return;
                }

                categories.forEach(category => {
                    const button = document.createElement('button');
                    button.className = 'category-btn';
                    const title = category.title?.id || category.title?.en || 'Kategori';
                    button.textContent = title;

                    if (category.icon?.path) {
                        const icon = document.createElement('img');
                        icon.src = category.icon.path;
                        icon.alt = title;
                        icon.className = 'category-icon';
                        button.prepend(icon);
                    }

                    button.setAttribute('data-category-id', category.id);

                    button.addEventListener('click', function () {
                        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        filterProducts(category.id);
                    });

                    categoryListElement.appendChild(button);
                });

                const firstBtn = categoryListElement.querySelector('.category-btn');
                if (firstBtn) {
                    firstBtn.classList.add('active');
                    const firstCategoryId = firstBtn.getAttribute('data-category-id');
                    if (firstCategoryId) {
                        filterProducts(firstCategoryId);
                    }
                }
            }

            function renderProducts(productData) {
                productListElement.innerHTML = '';

                if (!productData || productData.length === 0) {
                    productListElement.innerHTML = '<p>Tidak ada produk ditemukan.</p>';
                    return;
                }

                products = productData;

                products.forEach(product => {
                    const card = document.createElement('div');
                    card.className = 'product-card';
                    card.setAttribute('data-category-id', product.category_id);

                    const title = typeof product.title === 'string' ? product.title : (product.title?.id || product.title?.en || 'Produk Tanpa Judul');
                    const description = typeof product.description === 'string' ? product.description : (product.description?.id || product.description?.en || 'Tidak ada deskripsi');

                    const created = product.created_at ? new Date(product.created_at).toLocaleString('id-ID') : 'N/A';
                    const updated = product.updated_at ? new Date(product.updated_at).toLocaleString('id-ID') : 'N/A';

                    card.innerHTML = `
                        <div class="product-title">${title}</div>
                        <div class="product-description">${description}</div>
                        <div class="product-id">ID: ${product.id}</div>
                        <div class="timestamp">
                            <span>Dibuat: ${created}</span>
                            <span>Diperbarui: ${updated}</span>
                        </div>
                    `;

                    productListElement.appendChild(card);
                });

                const activeCategory = document.querySelector('.category-btn.active');
                if (activeCategory) {
                    filterProducts(activeCategory.getAttribute('data-category-id'));
                }
            }

            function filterProducts(categoryId) {
                const productCards = document.querySelectorAll('.product-card');
                let found = false;

                productCards.forEach(card => {
                    if (!categoryId || card.getAttribute('data-category-id') === categoryId) {
                        card.classList.add('active');
                        found = true;
                    } else {
                        card.classList.remove('active');
                    }
                });

                const msg = document.querySelector('.no-products-message');
                if (!found && productCards.length > 0) {
                    if (!msg) {
                        const p = document.createElement('p');
                        p.textContent = 'Tidak ada produk dalam kategori ini.';
                        p.className = 'no-products-message';
                        productListElement.appendChild(p);
                    }
                } else {
                    msg?.remove();
                }
            }

            async function fetchProducts() {
                try {
                    hideError();
                    const response = await fetch(`${categoryManager.apiBaseUrl}/products`);
                    if (!response.ok) throw new Error(`Error ${response.status}: ${response.statusText}`);
                    const data = await response.json();
                    renderProducts(data.data || []);
                } catch (error) {
                    showError(`Gagal memuat produk: ${error.message}`);
                    productListElement.innerHTML = '<p>Gagal memuat produk.</p>';
                }
            }

            async function initApp() {
                try {
                    await categoryManager.fetchCategories();
                    renderCategoryButtons(categoryManager.getAllCategories());
                    await fetchProducts();
                } catch (error) {
                    showError(`Terjadi kesalahan: ${error.message}`);
                }
            }

            initApp();
        });
    </script>
</body>
</html>

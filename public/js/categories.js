// File: categories.js
export default class CategoryManager {
    constructor(apiBaseUrl) {
        this.apiBaseUrl = apiBaseUrl;
        this.categories = [];
    }

    async fetchCategories() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/categories`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data && data.status === 'success' && data.data && Array.isArray(data.data.categories)) {
                this.categories = data.data.categories;
            } else if (data && data.status === 'success' && Array.isArray(data.data)) {
                this.categories = data.data;
            } else if (Array.isArray(data)) {
                this.categories = data;
            } else {
                console.warn('Format data kategori tidak valid:', data);
                this.categories = [];
            }
            return this.categories;
        } catch (error) {
            console.error('Gagal mengambil kategori:', error.message);
            this.categories = [];
            return [];
        }
    }

    getAllCategories() {
        return this.categories;
    }

    getCategoryById(id) {
        return this.categories.find(category => category.id === id);
    }
    
    // Fungsi untuk merender kategori ke DOM
    renderCategories(containerId) {
        const container = document.getElementById(containerId);
        
        if (!container) {
            console.error(`Element dengan id "${containerId}" tidak ditemukan`);
            return;
        }
        
        if (!this.categories || this.categories.length === 0) {
            container.innerHTML = '<p>Tidak ada kategori yang tersedia.</p>';
            return;
        }
        
        let html = '<div class="category-list">';
        this.categories.forEach(category => {
            const title = category.title?.id || category.title?.en || 'Tanpa Judul';
            html += `
                <div class="category-item" data-id="${category.id}">
                    <div class="category-title">${title}</div>
                </div>
            `;
        });
        html += '</div>';
        
        container.innerHTML = html;
        
        // Tambahkan event listener ke item kategori
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', () => {
                const categoryId = item.getAttribute('data-id');
                // Handler klik kategori
                this.handleCategoryClick(categoryId);
            });
        });
    }
    
    // Handler untuk klik kategori
    handleCategoryClick(categoryId) {
        console.log('Kategori diklik:', categoryId);
        // Tambahkan kode untuk menampilkan produk dalam kategori ini
        // Misalnya, memanggil fungsi untuk mengambil produk berdasarkan categoryId
    }
}
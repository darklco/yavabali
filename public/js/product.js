const apiUrl = "http://localhost:8000/api/products";

// Fungsi aman untuk parsing JSON
function safeParse(jsonStr, fallback = {}) {
  try {
    if (typeof jsonStr === 'object' && jsonStr !== null) return jsonStr;
    return JSON.parse(jsonStr);
  } catch (e) {
    console.warn("Gagal parse JSON:", jsonStr);
    return fallback;
  }
}

// Ambil teks dari field multibahasa
function getLocalizedText(field, fallback = 'Tidak tersedia') {
  const obj = safeParse(field, {});
  if (typeof obj !== 'object' || obj === null) return fallback;

  return obj.id || obj.en || Object.values(obj)[0] || fallback;
}

// Ambil URL gambar
function getImageUrl(field, fallback = 'https://via.placeholder.com/150') {
  const obj = safeParse(field, {});
  if (typeof obj !== 'object' || obj === null) return fallback;

  return obj.id || obj.en || Object.values(obj)[0] || fallback;
}

// Ambil data produk dari API
fetch(apiUrl)
  .then(response => {
    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
    return response.json();
  })
  .then(result => {
    const products = Array.isArray(result.data) ? result.data : [];

    const container = document.getElementById("product-list");
    if (!container) {
      console.error("Element #product-list tidak ditemukan.");
      return;
    }

    container.innerHTML = '';

    if (products.length === 0) {
      container.innerHTML = '<p>Tidak ada produk ditemukan.</p>';
      return;
    }

    products.forEach(product => {
      const title = getLocalizedText(product.title, "Produk Tanpa Judul");
      const description = getLocalizedText(product.description, "Tidak ada deskripsi");
      const imageUrl = getImageUrl(product.image);

      const div = document.createElement("div");
      div.className = "product-item";
      div.innerHTML = `
        <h2>${title}</h2>
        <img src="${imageUrl}" alt="${title}" width="150">
        <p>${description}</p>
        <hr>
      `;
      container.appendChild(div);
    });
  })
  .catch(error => {
    console.error("Gagal mengambil data:", error);
    const container = document.getElementById("product-list");
    if (container) {
      container.innerHTML = `
        <div class="error">
          <p>Gagal memuat data produk. Silakan coba lagi nanti.</p>
          <p>Error: ${error.message}</p>
        </div>
      `;
    }
  });

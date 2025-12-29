// ===============================
// GLOBAL DATA
// ===============================
let allData = [];

// ===============================
// FETCH DATA
// ===============================
fetch("json.php")
  .then((res) => {
    if (!res.ok) throw new Error("Gagal mengambil data");
    return res.json();
  })
  .then((data) => {
    allData = data;
    renderKatalog(allData);
    startAutoSlider();
  })
  .catch((err) => {
    console.error(err);
    const catalog = document.querySelector(".catalog");
    if (catalog) {
      catalog.innerHTML = `
        <p style="grid-column:1/-1;text-align:center;">
          Terjadi kesalahan saat memuat data.
        </p>`;
    }
  });

// ===============================
// RENDER KATALOG (FIXED)
// ===============================
function renderKatalog(data) {
  const catalog = document.querySelector(".catalog");
  if (!catalog) return;

  catalog.innerHTML = "";

  if (data.length === 0) {
    catalog.innerHTML = `
      <p style="grid-column:1/-1;text-align:center;">
        Belum ada data.
      </p>`;
    return;
  }

  data.forEach((item, index) => {
    const harga = Number(item.harga).toLocaleString("id-ID");
    const rating = (Math.random() * (5 - 4.7) + 4.7).toFixed(1);
    const terjual = item.total_terjual || 0;

    // ===== SLIDER IMAGE =====
    const images = item.gambar_file ? item.gambar_file.split(",") : [];
    let sliderHtml = "";

    if (images.length) {
      images.forEach((img, i) => {
        sliderHtml += `
          <img
            src="image.php?file=${item.akses_token}&img=${img.trim()}"
            class="slide ${i === 0 ? "active" : ""}"
            style="opacity:${i === 0 ? 1 : 0}"
          >
        `;
      });
    } else {
      sliderHtml = `<img src="placeholder.jpg" class="slide active">`;
    }

    // ===== DESKRIPSI =====
    const desc = item.deskripsi
      ? item.deskripsi
          .split(",")
          .map((d) => `<li>${d.trim()}</li>`)
          .join("")
      : "<li>Desain website modern</li>";

    const linkWA = `https://wa.me/6282363131543?text=${encodeURIComponent(
      `Halo Admin, saya tertarik dengan website: ${item.judul}`
    )}`;

    // ===== CARD =====
    catalog.innerHTML += `
      <div class="card card-website">

        <div class="badge-diskon">TERLARIS</div>

        <div class="card-img slider-container">
          <div class="slides" id="slides-${index}">
            ${sliderHtml}
          </div>

          ${
            images.length > 1
              ? `
              <button class="prev" onclick="moveSlide(${index},-1)">❮</button>
              <button class="next" onclick="moveSlide(${index},1)">❯</button>
            `
              : ""
          }

          <span class="lock">✨ ${item.kategori || "Premium"}</span>
        </div>

        <div class="card-body">
          <h3 class="card-title">${item.judul}</h3>

          <div class="sold-info">
            <span class="star-rating">⭐ ${rating}</span>
            <span>|</span>
            <span>${terjual} Terjual</span>
          </div>

          <div class="description-scroll">
            <ol>${desc}</ol>
          </div>

          <div class="price-tag">
            <span>Mulai dari</span>
            <strong>Rp ${harga}</strong>
          </div>

          <a href="view.php?file=${
            item.akses_token
          }" target="_blank" class="btn-demo">
            Cek Demo
          </a>

          <a href="${linkWA}" target="_blank" class="btn-wa">
            Beli via WhatsApp
          </a>
        </div>

      </div>
    `;
  });
}

// ===============================
// SLIDER MANUAL
// ===============================
function moveSlide(index, direction) {
  const slides = document.querySelectorAll(`#slides-${index} .slide`);
  if (!slides.length) return;

  let active = [...slides].findIndex((s) => s.classList.contains("active"));
  slides[active].classList.remove("active");
  slides[active].style.opacity = 0;

  active = (active + direction + slides.length) % slides.length;

  slides[active].classList.add("active");
  slides[active].style.opacity = 1;
}

// ===============================
// AUTO SLIDER
// ===============================
function startAutoSlider() {
  setInterval(() => {
    allData.forEach((_, i) => {
      const slides = document.querySelectorAll(`#slides-${i} .slide`);
      if (slides.length > 1) moveSlide(i, 1);
    });
  }, 5000);
}

// ===============================
// FILTER
// ===============================
function filterKatalog(kategori, el) {
  document
    .querySelectorAll(".tag")
    .forEach((t) => t.classList.remove("active"));
  el.classList.add("active");

  if (kategori === "Semua") {
    renderKatalog(allData);
  } else {
    renderKatalog(
      allData.filter(
        (item) =>
          (item.kategori || "").toLowerCase().trim() ===
          kategori.toLowerCase().trim()
      )
    );
  }
}

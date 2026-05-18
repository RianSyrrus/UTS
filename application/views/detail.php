<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ShopKu - Detail Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        h1,
        h2,
        h3 {
            font-family: 'Playfair Display', serif;
        }

        .navbar-shadow {
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }

        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .btn-primary {
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            transform: scale(1.03);
        }

        .img-zoom {
            transition: transform 0.4s ease;
        }

        .img-zoom:hover {
            transform: scale(1.06);
        }
    </style>
</head>

<body class="bg-slate-50">

    <div id="root"></div>

    <!-- Pass product ID dari CI3 ke React -->
    <script>
        var PRODUCT_ID = parseInt(window.location.pathname.split('/').pop()) || 1;
    </script>

    <script type="text/babel">
        const { useState, useEffect } = React;

// =====================
// NAVBAR
// =====================
function Navbar({ cartCount }) {
  const [menuOpen, setMenuOpen] = useState(false);
  return (
    <nav className="bg-white navbar-shadow sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="<?= base_url('home') ?>" className="text-2xl font-bold text-slate-800" style={{fontFamily:'Playfair Display, serif'}}>
          Shop<span className="text-blue-600">Ku</span>
        </a>
        <ul className="hidden md:flex gap-8 text-slate-600 font-medium">
          <li><a href="<?= base_url('home') ?>" className="hover:text-blue-600 transition">Home</a></li>
          <li><a href="<?= base_url('katalog') ?>" className="hover:text-blue-600 transition">Katalog</a></li>
          <li><a href="<?= base_url('login') ?>" className="hover:text-blue-600 transition">Login</a></li>
        </ul>
        <div className="flex items-center gap-4">
          <a href="<?= base_url('cart') ?>" className="relative">
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-slate-700 hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {cartCount > 0 && (
              <span className="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {cartCount}
              </span>
            )}
          </a>
          <button className="md:hidden" onClick={() => setMenuOpen(!menuOpen)}>
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
      {menuOpen && (
        <div className="md:hidden bg-white border-t px-4 py-3 flex flex-col gap-3 text-slate-700 font-medium">
          <a href="<?= base_url('home') ?>" className="hover:text-blue-600">Home</a>
          <a href="<?= base_url('katalog') ?>" className="hover:text-blue-600">Katalog</a>
          <a href="<?= base_url('login') ?>">Login</a>
        </div>
      )}
    </nav>
  );
}

// =====================
// SKELETON DETAIL
// =====================
function SkeletonDetail() {
  return (
    <div className="max-w-5xl mx-auto px-4 py-12">
      <div className="bg-white rounded-3xl shadow-sm p-8 flex flex-col md:flex-row gap-10">
        <div className="skeleton w-full md:w-80 h-80 rounded-2xl flex-shrink-0"></div>
        <div className="flex-1 space-y-4 py-4">
          <div className="skeleton h-4 w-1/4 rounded"></div>
          <div className="skeleton h-8 w-3/4 rounded"></div>
          <div className="skeleton h-4 w-1/3 rounded"></div>
          <div className="skeleton h-4 w-full rounded"></div>
          <div className="skeleton h-4 w-full rounded"></div>
          <div className="skeleton h-4 w-2/3 rounded"></div>
          <div className="skeleton h-12 w-48 rounded-xl mt-6"></div>
        </div>
      </div>
    </div>
  );
}

// =====================
// STAR RATING
// =====================
function StarRating({ rate, count }) {
  const stars = Math.round(rate);
  return (
    <div className="flex items-center gap-2">
      <div className="flex">
        {[...Array(5)].map((_, i) => (
          <span key={i} style={{color: i < stars ? '#f59e0b' : '#cbd5e1', fontSize: '18px'}}>★</span>
        ))}
      </div>
      <span className="text-slate-500 text-sm">({count} ulasan)</span>
    </div>
  );
}

// =====================
// DETAIL PRODUK
// =====================
function DetailProduk({ onAddCart }) {
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [qty, setQty] = useState(1);
  const [activeTab, setActiveTab] = useState('deskripsi');

  useEffect(() => {
    axios.get(`https://fakestoreapi.com/products/${PRODUCT_ID}`)
      .then(res => {
        setProduct(res.data);
        setLoading(false);
      })
      .catch(() => {
        setError('Produk tidak ditemukan.');
        setLoading(false);
      });
  }, []);

  if (loading) return <SkeletonDetail />;

  if (error) return (
    <div className="max-w-5xl mx-auto px-4 py-20 text-center">
      <div className="text-6xl mb-4">😕</div>
      <h2 className="text-2xl font-bold text-slate-700 mb-2">{error}</h2>
      <a href="<?= base_url('katalog') ?>" className="text-blue-600 hover:underline">Kembali ke Katalog</a>
    </div>
  );

  return (
    <main className="max-w-5xl mx-auto px-4 py-12">
      {/* Breadcrumb */}
      <nav className="text-sm text-slate-400 mb-6 flex items-center gap-2">
        <a href="<?= base_url('home') ?>" className="hover:text-blue-600 transition">Home</a>
        <span>›</span>
        <a href="<?= base_url('katalog') ?>" className="hover:text-blue-600 transition">Katalog</a>
        <span>›</span>
        <span className="text-slate-600 line-clamp-1">{product.title}</span>
      </nav>

      {/* Main Card */}
      <div className="bg-white rounded-3xl shadow-sm p-8 flex flex-col md:flex-row gap-10">
        {/* Gambar Produk */}
        <div className="w-full md:w-80 flex-shrink-0 flex items-center justify-center bg-slate-50 rounded-2xl p-8 h-80">
          <img
            src={product.image}
            alt={product.title}
            className="max-h-full max-w-full object-contain img-zoom"
          />
        </div>

        {/* Info Produk */}
        <div className="flex-1">
          <span className="text-xs font-semibold text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full">
            {product.category}
          </span>

          <h1 className="text-2xl md:text-3xl font-bold text-slate-800 mt-3 mb-3 leading-snug">
            {product.title}
          </h1>

          <StarRating rate={product.rating.rate} count={product.rating.count} />

          <div className="text-3xl font-bold text-blue-600 mt-4 mb-6">
            ${product.price}
          </div>

          {/* Quantity */}
          <div className="flex items-center gap-4 mb-6">
            <span className="text-slate-600 font-medium">Jumlah:</span>
            <div className="flex items-center border border-slate-200 rounded-xl overflow-hidden">
              <button
                onClick={() => setQty(prev => Math.max(1, prev - 1))}
                className="px-4 py-2 text-slate-600 hover:bg-slate-100 transition font-bold"
              >−</button>
              <span className="px-4 py-2 font-semibold text-slate-800 min-w-[40px] text-center">{qty}</span>
              <button
                onClick={() => setQty(prev => prev + 1)}
                className="px-4 py-2 text-slate-600 hover:bg-slate-100 transition font-bold"
              >+</button>
            </div>
          </div>

          {/* Tombol Aksi */}
          <div className="flex flex-col sm:flex-row gap-3">
            <button
              onClick={() => onAddCart(product, qty)}
              className="btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl flex items-center justify-center gap-2"
            >
              🛒 Tambah ke Keranjang
            </button>
            <a
              href="<?= base_url('checkout') ?>"
              className="btn-primary bg-slate-800 hover:bg-slate-900 text-white font-semibold px-8 py-3 rounded-xl text-center"
            >
              Beli Sekarang
            </a>
          </div>

          {/* Info Pengiriman */}
          <div className="mt-6 flex flex-col gap-2 text-sm text-slate-500">
            <span>🚚 Gratis ongkir min. Rp 100.000</span>
            <span>🔒 Transaksi aman & terjamin</span>
            <span>↩️ Garansi return 30 hari</span>
          </div>
        </div>
      </div>

      {/* Tab Deskripsi */}
      <div className="bg-white rounded-3xl shadow-sm p-8 mt-6">
        <div className="flex gap-6 border-b border-slate-100 mb-6">
          {['deskripsi', 'ulasan'].map(tab => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`pb-3 text-sm font-semibold capitalize transition border-b-2 ${
                activeTab === tab
                  ? 'text-blue-600 border-blue-600'
                  : 'text-slate-400 border-transparent hover:text-slate-600'
              }`}
            >
              {tab}
            </button>
          ))}
        </div>

        {/* Conditional Rendering berdasarkan tab */}
        {activeTab === 'deskripsi' ? (
          <p className="text-slate-600 leading-relaxed">{product.description}</p>
        ) : (
          <div className="text-center py-8 text-slate-400">
            <div className="text-4xl mb-2">💬</div>
            <p>Belum ada ulasan untuk produk ini.</p>
          </div>
        )}
      </div>
    </main>
  );
}

// =====================
// FOOTER
// =====================
function Footer() {
  return (
    <footer className="bg-slate-800 text-slate-300 mt-8">
      <div className="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 className="text-white text-xl font-bold mb-3" style={{fontFamily:'Playfair Display, serif'}}>
            Shop<span className="text-blue-400">Ku</span>
          </h3>
          <p className="text-sm leading-relaxed">Platform belanja online terpercaya dengan ribuan produk pilihan.</p>
        </div>
        <div>
          <h4 className="text-white font-semibold mb-3">Navigasi</h4>
          <ul className="space-y-2 text-sm">
            <li><a href="<?= base_url('home') ?>" className="hover:text-white transition">Home</a></li>
            <li><a href="<?= base_url('katalog') ?>" className="hover:text-white transition">Katalog</a></li>
            <li><a href="<?= base_url('cart') ?>" className="hover:text-white transition">Keranjang</a></li>
            <li><a href="<?= base_url('login') ?>" className="hover:text-white transition">Login</a></li>
          </ul>
        </div>
        <div>
          <h4 className="text-white font-semibold mb-3">Kontak</h4>
          <ul className="space-y-2 text-sm">
            <li>📧 shopku@email.com</li>
            <li>📱 0812-3456-7890</li>
            <li>📍 Kudus, Jawa Tengah</li>
          </ul>
        </div>
      </div>
      <div className="border-t border-slate-700 text-center py-4 text-sm text-slate-500">
        © 2024 ShopKu. All rights reserved.
      </div>
    </footer>
  );
}

// =====================
// APP ROOT
// =====================
function App() {
  const [cartCount, setCartCount] = useState(0);

  const handleAddCart = (product, qty) => {
    setCartCount(prev => prev + qty);
    alert(`${qty}x "${product.title.substring(0, 30)}..." ditambahkan ke keranjang!`);
  };

  return (
    <div>
      <Navbar cartCount={cartCount} />
      <DetailProduk onAddCart={handleAddCart} />
      <Footer />
    </div>
  );
}

ReactDOM.render(<App />, document.getElementById('root'));
</script>

</body>

</html>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ShopKu - Katalog Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
    <!-- Shared stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body class="bg-slate-50">
    <div id="root"></div>

    <script type="text/babel">
        const { useState, useEffect, useMemo } = React;

        /* ===================== NAVBAR ===================== */
        function Navbar({ cartCount }) {
            const [menuOpen, setMenuOpen] = useState(false);
            return (
                <nav className="bg-white navbar-shadow sticky top-0 z-50">
                    <div className="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                        <a href="<?= base_url('home') ?>" className="text-2xl font-bold text-slate-800" style={{fontFamily:'Playfair Display, serif'}}>
                            Shop<span className="text-blue-600">Ku</span>
                        </a>
                        <ul className="hidden md:flex gap-8 text-slate-600 font-medium">
                            <li><a href="<?= base_url('home') ?>"    className="hover:text-blue-600 transition">Home</a></li>
                            <li><a href="<?= base_url('katalog') ?>" className="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">Katalog</a></li>
                            <li><a href="<?= base_url('login') ?>"   className="hover:text-blue-600 transition">Login</a></li>
                        </ul>
                        <div className="flex items-center gap-4">
                            <a href="<?= base_url('cart') ?>" className="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-slate-700 hover:text-blue-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-9H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {cartCount > 0 && (
                                    <span className="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{cartCount}</span>
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
                            <a href="<?= base_url('home') ?>">Home</a>
                            <a href="<?= base_url('katalog') ?>" className="text-blue-600 font-semibold">Katalog</a>
                            <a href="<?= base_url('login') ?>">Login</a>
                        </div>
                    )}
                </nav>
            );
        }

        /* ===================== PAGE HEADER ===================== */
        function PageHeader({ totalProducts, keyword }) {
            return (
                <header className="hero-bg text-white py-10 px-4">
                    <div className="max-w-7xl mx-auto">
                        <p className="text-blue-300 text-sm font-medium mb-1">🛍️ Semua Produk</p>
                        <h1 className="text-3xl md:text-4xl font-bold mb-2">Katalog Produk</h1>
                        <p className="text-slate-400 text-sm">
                            {keyword
                                ? `Hasil pencarian untuk "${keyword}" — ${totalProducts} produk ditemukan`
                                : `Menampilkan ${totalProducts} produk pilihan terbaik`}
                        </p>
                    </div>
                </header>
            );
        }

        /* ===================== SKELETON ===================== */
        function SkeletonCard() {
            return (
                <div className="bg-white rounded-2xl overflow-hidden shadow-sm">
                    <div className="skeleton h-52 w-full"></div>
                    <div className="p-4 space-y-3">
                        <div className="skeleton h-3 w-2/4 rounded"></div>
                        <div className="skeleton h-4 w-3/4 rounded"></div>
                        <div className="skeleton h-4 w-full rounded"></div>
                        <div className="skeleton h-8 w-full rounded-lg mt-2"></div>
                    </div>
                </div>
            );
        }

        /* ===================== PRODUCT CARD (grid) ===================== */
        function ProductCard({ product, onAddCart }) {
            const stars = Math.round(product.rating?.rate || 4);
            return (
                <div className="bg-white rounded-2xl overflow-hidden shadow-sm card-hover border border-slate-100 flex flex-col">
                    <a href={`<?= base_url('detail') ?>/${product.id}`}>
                        <div className="h-52 flex items-center justify-center bg-slate-50 p-4 relative overflow-hidden">
                            {product.rating?.count > 200 && (
                                <span className="absolute top-2 left-2 badge-hot">🔥 Hot</span>
                            )}
                            <img src={product.image} alt={product.title}
                                className="max-h-full max-w-full object-contain img-zoom" />
                        </div>
                    </a>
                    <div className="p-4 flex flex-col flex-1">
                        <p className="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-1">{product.category}</p>
                        <h3 className="text-slate-800 font-semibold text-sm line-clamp-2 mb-2 leading-snug flex-1">{product.title}</h3>
                        <div className="flex items-center gap-1 mb-3">
                            {[...Array(5)].map((_, i) => (
                                <span key={i} className={i < stars ? 'star' : 'text-slate-300'} style={{fontSize:'13px'}}>★</span>
                            ))}
                            <span className="text-xs text-slate-400 ml-1">({product.rating?.count || 0})</span>
                        </div>
                        <div className="flex items-center justify-between mt-auto">
                            <span className="text-blue-700 font-bold text-lg">${product.price}</span>
                            <button onClick={() => onAddCart(product)}
                                className="btn-primary bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg">
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            );
        }

        /* ===================== PRODUCT LIST CARD ===================== */
        function ProductListCard({ product, onAddCart }) {
            const stars = Math.round(product.rating?.rate || 4);
            return (
                <div className="bg-white rounded-2xl overflow-hidden shadow-sm card-hover border border-slate-100 flex gap-4 p-4">
                    <a href={`<?= base_url('detail') ?>/${product.id}`} className="flex-shrink-0">
                        <div className="w-28 h-28 flex items-center justify-center bg-slate-50 rounded-xl p-2">
                            <img src={product.image} alt={product.title} className="max-h-full max-w-full object-contain" />
                        </div>
                    </a>
                    <div className="flex flex-1 flex-col justify-between">
                        <div>
                            <p className="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-0.5">{product.category}</p>
                            <h3 className="text-slate-800 font-semibold text-sm leading-snug line-clamp-2 mb-1">{product.title}</h3>
                            <p className="text-slate-400 text-xs line-clamp-2">{product.description}</p>
                        </div>
                        <div className="flex items-center justify-between mt-2">
                            <div>
                                <div className="flex items-center gap-1 mb-0.5">
                                    {[...Array(5)].map((_, i) => (
                                        <span key={i} className={i < stars ? 'star' : 'text-slate-300'} style={{fontSize:'12px'}}>★</span>
                                    ))}
                                    <span className="text-xs text-slate-400 ml-1">({product.rating?.count || 0})</span>
                                </div>
                                <span className="text-blue-700 font-bold text-lg">${product.price}</span>
                            </div>
                            <button onClick={() => onAddCart(product)}
                                className="btn-primary bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            );
        }

        /* ===================== FILTER SIDEBAR ===================== */
        function FilterSidebar({ categories, activeCategory, onCategory, maxPrice, onMaxPrice, sortBy, onSort, onReset }) {
            const CAT_LABEL = {
                "all": "Semua", "electronics": "Elektronik",
                "jewelery": "Perhiasan", "men's clothing": "Pria", "women's clothing": "Wanita",
            };
            const sortOptions = [
                { value: "default",    label: "Default" },
                { value: "price-asc",  label: "Harga: Terendah" },
                { value: "price-desc", label: "Harga: Tertinggi" },
                { value: "rating",     label: "Rating Terbaik" },
                { value: "popular",    label: "Terpopuler" },
            ];
            return (
                <aside className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-6 h-fit sticky top-24">
                    <div className="flex items-center justify-between">
                        <h2 className="text-slate-800 font-bold text-lg">Filter</h2>
                        <button onClick={onReset} className="text-xs text-blue-600 hover:underline font-medium">Reset</button>
                    </div>
                    {/* Kategori */}
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3">Kategori</p>
                        <div className="flex flex-col gap-2">
                            {["all", ...categories].map(cat => (
                                <button key={cat} onClick={() => onCategory(cat)}
                                    className={`filter-chip text-left text-sm px-3 py-2 rounded-lg border font-medium ${
                                        activeCategory === cat ? 'active' : 'bg-slate-50 text-slate-700 border-slate-200'
                                    }`}>
                                    {CAT_LABEL[cat] || cat}
                                </button>
                            ))}
                        </div>
                    </div>
                    {/* Harga */}
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3">Harga Maksimal</p>
                        <input type="range" min="0" max="1000" step="50" value={maxPrice}
                            onChange={e => onMaxPrice(Number(e.target.value))} />
                        <div className="flex justify-between text-xs text-slate-500 mt-1">
                            <span>$0</span>
                            <span className="font-semibold text-blue-600">${maxPrice}</span>
                            <span>$1000</span>
                        </div>
                    </div>
                    {/* Sort */}
                    <div>
                        <p className="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3">Urutkan</p>
                        <select value={sortBy} onChange={e => onSort(e.target.value)}
                            className="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-slate-700 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {sortOptions.map(opt => <option key={opt.value} value={opt.value}>{opt.label}</option>)}
                        </select>
                    </div>
                </aside>
            );
        }

        /* ===================== SEARCH TOOLBAR ===================== */
        function SearchToolbar({ keyword, onKeyword, total, view, onView }) {
            return (
                <div className="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-6">
                    <form onSubmit={e => e.preventDefault()}
                        className="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-sm w-full sm:w-80"
                        role="search" aria-label="Cari produk">
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="search" placeholder="Cari produk..." value={keyword}
                            onChange={e => onKeyword(e.target.value)}
                            className="flex-1 text-sm text-slate-700 bg-transparent focus:outline-none"
                            aria-label="Kata kunci pencarian" />
                        {keyword && (
                            <button type="button" onClick={() => onKeyword('')} className="text-slate-400 hover:text-slate-600">✕</button>
                        )}
                    </form>
                    <div className="flex items-center gap-3">
                        <span className="text-sm text-slate-500">{total} produk</span>
                        <div className="flex gap-1 bg-white border border-slate-200 rounded-lg p-1">
                            {['grid', 'list'].map(v => (
                                <button key={v} onClick={() => onView(v)}
                                    className={`p-1.5 rounded ${view === v ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-slate-600'}`}
                                    title={v + ' view'}>
                                    {v === 'grid'
                                        ? <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                        : <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" /></svg>
                                    }
                                </button>
                            ))}
                        </div>
                    </div>
                </div>
            );
        }

        /* ===================== EMPTY STATE ===================== */
        function EmptyState({ onReset }) {
            return (
                <div className="flex flex-col items-center justify-center py-20 text-center">
                    <div className="text-6xl mb-4">🔍</div>
                    <h3 className="text-slate-700 font-bold text-xl mb-2">Produk tidak ditemukan</h3>
                    <p className="text-slate-400 text-sm mb-6">Coba ubah filter atau kata kunci pencarian kamu</p>
                    <button onClick={onReset} className="btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-xl">
                        Reset Filter
                    </button>
                </div>
            );
        }

        /* ===================== TOAST ===================== */
        function Toast({ message, visible }) {
            if (!visible) return null;
            return (
                <div className="toast-enter fixed bottom-6 right-6 bg-slate-800 text-white text-sm font-medium px-5 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2">
                    <span className="text-green-400">✓</span> {message}
                </div>
            );
        }

        /* ===================== FOOTER ===================== */
        function Footer() {
            return (
                <footer className="bg-slate-800 text-slate-300 mt-16">
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
                                <li><a href="<?= base_url('home') ?>"    className="hover:text-white transition">Home</a></li>
                                <li><a href="<?= base_url('katalog') ?>" className="hover:text-white transition">Katalog</a></li>
                                <li><a href="<?= base_url('cart') ?>"    className="hover:text-white transition">Keranjang</a></li>
                                <li><a href="<?= base_url('login') ?>"   className="hover:text-white transition">Login</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="text-white font-semibold mb-3">Kontak</h4>
                            <ul className="space-y-2 text-sm">
                                <li>📧 oiqgamink27@gmail.com</li>
                                <li>📱 0877-0445-1209</li>
                                <li>📍 AMIKOM    YOGYAKARTA</li>
                            </ul>
                        </div>
                    </div>
                    <div className="border-t border-slate-700 text-center py-4 text-sm text-slate-500">
                        © 2026 ShopKu. All rights reserved.
                    </div>
                </footer>
            );
        }

        /* ===================== APP ROOT ===================== */
        function App() {
            const [allProducts, setAllProducts]       = useState([]);
            const [categories, setCategories]         = useState([]);
            const [loading, setLoading]               = useState(true);
            const [error, setError]                   = useState(null);
            const [keyword, setKeyword]               = useState('');
            const [activeCategory, setActiveCategory] = useState('all');
            const [maxPrice, setMaxPrice]             = useState(1000);
            const [sortBy, setSortBy]                 = useState('default');
            const [viewMode, setViewMode]             = useState('grid');
            const [cartCount, setCartCount]           = useState(0);
            const [toast, setToast]                   = useState({ visible: false, message: '' });

            useEffect(() => {
                setLoading(true);
                axios.get('https://fakestoreapi.com/products')
                    .then(res => {
                        setAllProducts(res.data);
                        setCategories([...new Set(res.data.map(p => p.category))]);
                        setLoading(false);
                    })
                    .catch(() => { setError('Gagal memuat produk. Silakan coba lagi.'); setLoading(false); });
            }, []);

            const filteredProducts = useMemo(() => {
                let r = [...allProducts];
                if (activeCategory !== 'all') r = r.filter(p => p.category === activeCategory);
                r = r.filter(p => p.price <= maxPrice);
                if (keyword.trim()) {
                    const q = keyword.toLowerCase();
                    r = r.filter(p => p.title.toLowerCase().includes(q) || p.category.toLowerCase().includes(q) || p.description.toLowerCase().includes(q));
                }
                if (sortBy === 'price-asc')  r.sort((a, b) => a.price - b.price);
                if (sortBy === 'price-desc') r.sort((a, b) => b.price - a.price);
                if (sortBy === 'rating')     r.sort((a, b) => (b.rating?.rate || 0) - (a.rating?.rate || 0));
                if (sortBy === 'popular')    r.sort((a, b) => (b.rating?.count || 0) - (a.rating?.count || 0));
                return r;
            }, [allProducts, activeCategory, maxPrice, keyword, sortBy]);

            const handleAddCart = (product) => {
                setCartCount(prev => prev + 1);
                setToast({ visible: true, message: `"${product.title.substring(0, 25)}..." ditambahkan!` });
                setTimeout(() => setToast({ visible: false, message: '' }), 2500);
            };

            const handleReset = () => {
                setKeyword(''); setActiveCategory('all'); setMaxPrice(1000); setSortBy('default');
            };

            return (
                <div>
                    <Navbar cartCount={cartCount} />
                    <PageHeader totalProducts={filteredProducts.length} keyword={keyword} />

                    <main className="max-w-7xl mx-auto px-4 py-8">
                        <div className="flex flex-col lg:flex-row gap-8">
                            <div className="w-full lg:w-64 flex-shrink-0">
                                <FilterSidebar
                                    categories={categories} activeCategory={activeCategory} onCategory={setActiveCategory}
                                    maxPrice={maxPrice} onMaxPrice={setMaxPrice}
                                    sortBy={sortBy} onSort={setSortBy} onReset={handleReset} />
                            </div>
                            <div className="flex-1 min-w-0">
                                <SearchToolbar keyword={keyword} onKeyword={setKeyword}
                                    total={filteredProducts.length} view={viewMode} onView={setViewMode} />
                                {error && (
                                    <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 text-sm mb-6">⚠️ {error}</div>
                                )}
                                {loading ? (
                                    <div className={viewMode === 'grid' ? "grid grid-cols-2 md:grid-cols-3 gap-5" : "flex flex-col gap-4"}>
                                        {[...Array(9)].map((_, i) => <SkeletonCard key={i} />)}
                                    </div>
                                ) : filteredProducts.length === 0 ? (
                                    <EmptyState onReset={handleReset} />
                                ) : viewMode === 'grid' ? (
                                    <div className="grid grid-cols-2 md:grid-cols-3 gap-5">
                                        {filteredProducts.map(p => <ProductCard key={p.id} product={p} onAddCart={handleAddCart} />)}
                                    </div>
                                ) : (
                                    <div className="flex flex-col gap-4">
                                        {filteredProducts.map(p => <ProductListCard key={p.id} product={p} onAddCart={handleAddCart} />)}
                                    </div>
                                )}
                            </div>
                        </div>
                    </main>

                    <Footer />
                    <Toast visible={toast.visible} message={toast.message} />
                </div>
            );
        }

        ReactDOM.render(<App />, document.getElementById('root'));
    </script>
</body>

</html>
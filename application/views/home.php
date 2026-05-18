<?php $title = 'ShopKu - Belanja Online';
$this->load->view('templates/header'); ?>
</head>

<body class="bg-slate-50">
    <div id="root"></div>

    <script type="text/babel">
        const { useState, useEffect } = React;

        /* =====================
           NAVBAR
        ===================== */
        function Navbar({ cartCount }) {
            const [menuOpen, setMenuOpen] = useState(false);
            return (
                <nav className="bg-white navbar-shadow sticky top-0 z-50">
                    <div className="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                        <a href="<?= base_url('home') ?>" className="text-2xl font-bold text-slate-800" style={{fontFamily:'Playfair Display, serif'}}>
                            Shop<span className="text-blue-600">Ku</span>
                        </a>
                        <ul className="hidden md:flex gap-8 text-slate-600 font-medium">
                            <li><a href="<?= base_url('home') ?>" className="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">Home</a></li>
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
                            <a href="<?= base_url('home') ?>" className="text-blue-600 font-semibold">Home</a>
                            <a href="<?= base_url('katalog') ?>" className="hover:text-blue-600">Katalog</a>
                            <a href="<?= base_url('login') ?>">Login</a>
                        </div>
                    )}
                </nav>
            );
        }

        /* =====================
           HERO
        ===================== */
        function Hero() {
            return (
                <header className="hero-bg text-white py-24 px-4">
                    <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12">
                        <div className="flex-1 text-center md:text-left">
                            <span className="bg-blue-500 bg-opacity-30 text-blue-200 text-sm font-semibold px-3 py-1 rounded-full mb-4 inline-block">
                                🔥 Promo Spesial Hari Ini
                            </span>
                            <h1 className="text-4xl md:text-6xl font-bold leading-tight mb-4">
                                Belanja Lebih<br/>
                                <span className="text-blue-400">Mudah &amp; Hemat</span>
                            </h1>
                            <p className="text-slate-300 text-lg mb-8 max-w-md">
                                Temukan ribuan produk pilihan dengan harga terbaik. Gratis ongkir untuk pembelian pertama!
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                                <a href="<?= base_url('katalog') ?>" className="btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl text-center">
                                    Belanja Sekarang
                                </a>
                                <a href="#produk-unggulan" className="border border-slate-400 text-slate-200 hover:border-white font-semibold px-8 py-3 rounded-xl text-center transition">
                                    Lihat Produk
                                </a>
                            </div>
                        </div>
                        <div className="flex-1 flex justify-center">
                            <div className="bg-white bg-opacity-10 rounded-3xl p-8 w-72 h-72 flex items-center justify-center border border-white border-opacity-20">
                                <div className="text-center">
                                    <div className="text-6xl mb-4">🛍️</div>
                                    <p className="text-blue-200 font-semibold">10.000+ Produk</p>
                                    <p className="text-slate-400 text-sm">Siap dikirim ke seluruh Indonesia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            );
        }

        /* =====================
           BANNER PROMO
        ===================== */
        function BannerPromo() {
            return (
                <section className="max-w-7xl mx-auto px-4 py-8">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {[
                            { icon: '🚚', title: 'Gratis Ongkir',     desc: 'Min. pembelian Rp 100rb' },
                            { icon: '🔒', title: 'Pembayaran Aman',   desc: 'Transaksi terjamin 100%'  },
                            { icon: '↩️', title: 'Mudah Return',      desc: 'Garansi 30 hari'          },
                        ].map((item, i) => (
                            <div key={i} className="bg-white rounded-2xl p-5 flex items-center gap-4 shadow-sm border border-slate-100">
                                <span className="text-3xl">{item.icon}</span>
                                <div>
                                    <p className="font-semibold text-slate-800">{item.title}</p>
                                    <p className="text-slate-500 text-sm">{item.desc}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>
            );
        }

        /* =====================
           SKELETON CARD
        ===================== */
        function SkeletonCard() {
            return (
                <div className="bg-white rounded-2xl overflow-hidden shadow-sm">
                    <div className="skeleton h-52 w-full"></div>
                    <div className="p-4 space-y-3">
                        <div className="skeleton h-4 w-3/4 rounded"></div>
                        <div className="skeleton h-4 w-1/2 rounded"></div>
                        <div className="skeleton h-8 w-full rounded-lg"></div>
                    </div>
                </div>
            );
        }

        /* =====================
           PRODUCT CARD
        ===================== */
        function ProductCard({ product, onAddCart }) {
            return (
                <div className="bg-white rounded-2xl overflow-hidden shadow-sm card-hover border border-slate-100">
                    <a href={`<?= base_url('detail') ?>/${product.id}`}>
                        <div className="h-52 flex items-center justify-center bg-slate-50 p-4 overflow-hidden">
                            <img
                                src={product.image}
                                alt={product.title}
                                className="max-h-full max-w-full object-contain img-zoom"
                            />
                        </div>
                    </a>
                    <div className="p-4">
                        <p className="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-1">{product.category}</p>
                        <h3 className="text-slate-800 font-semibold text-sm line-clamp-2 mb-2 leading-snug">
                            {product.title}
                        </h3>
                        <div className="flex items-center justify-between mt-3">
                            <span className="text-blue-700 font-bold text-lg">${product.price}</span>
                            <button
                                onClick={() => onAddCart(product)}
                                className="btn-primary bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg"
                            >
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            );
        }

        /* =====================
           FEATURED PRODUCTS
        ===================== */
        function FeaturedProducts({ onAddCart }) {
            const [products, setProducts] = useState([]);
            const [loading, setLoading]   = useState(true);
            const [error, setError]       = useState(null);

            useEffect(() => {
                axios.get('https://fakestoreapi.com/products?limit=8')
                    .then(res => { setProducts(res.data); setLoading(false); })
                    .catch(() => { setError('Gagal memuat produk.'); setLoading(false); });
            }, []);

            return (
                <main id="produk-unggulan" className="max-w-7xl mx-auto px-4 py-16">
                    <div className="text-center mb-10">
                        <h2 className="text-3xl font-bold text-slate-800 mb-2">Produk Unggulan</h2>
                        <p className="text-slate-500">Pilihan terbaik untuk kamu hari ini</p>
                    </div>

                    {error && (
                        <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 text-sm mb-6">
                            ⚠️ {error}
                        </div>
                    )}

                    {loading ? (
                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            {[...Array(8)].map((_, i) => <SkeletonCard key={i} />)}
                        </div>
                    ) : (
                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            {products.map(product => (
                                <ProductCard key={product.id} product={product} onAddCart={onAddCart} />
                            ))}
                        </div>
                    )}

                    <div className="text-center mt-10">
                        <a href="<?= base_url('katalog') ?>" className="btn-primary inline-block bg-slate-800 hover:bg-slate-900 text-white font-semibold px-10 py-3 rounded-xl transition">
                            Lihat Semua Produk →
                        </a>
                    </div>
                </main>
            );
        }

        /* =====================
           TOAST
        ===================== */
        function Toast({ message, visible }) {
            if (!visible) return null;
            return (
                <div className="toast-enter fixed bottom-6 right-6 bg-slate-800 text-white text-sm font-medium px-5 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2">
                    <span className="text-green-400">✓</span> {message}
                </div>
            );
        }

        /* =====================
           FOOTER
        ===================== */
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

        /* =====================
           APP ROOT
        ===================== */
        function App() {
            const [cartCount, setCartCount] = useState(0);
            const [toast, setToast]         = useState({ visible: false, message: '' });

            const handleAddCart = (product) => {
                setCartCount(prev => prev + 1);
                setToast({ visible: true, message: `"${product.title.substring(0, 28)}..." ditambahkan!` });
                setTimeout(() => setToast({ visible: false, message: '' }), 2500);
            };

            return (
                <div>
                    <Navbar cartCount={cartCount} />
                    <Hero />
                    <BannerPromo />
                    <FeaturedProducts onAddCart={handleAddCart} />
                    <Footer />
                    <Toast visible={toast.visible} message={toast.message} />
                </div>
            );
        }

        ReactDOM.render(<App />, document.getElementById('root'));
    </script>
    <?php $this->load->view('templates/footer'); ?>
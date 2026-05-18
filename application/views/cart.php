<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ShopKu - Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        .cart-item-enter {
            animation: slideIn 0.3s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .cart-item-remove {
            animation: fadeOut 0.25s ease forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }

            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }
    </style>
</head>

<body class="bg-slate-50">
    <div id="root"></div>

    <script type="text/babel">
        const { useState, useEffect, useMemo } = React;

        /* ─────────────────────────────────────────
           INITIAL CART (3 produk default dari API)
           ───────────────────────────────────────── */
        const DEFAULT_IDS = [1, 2, 3];

        /* ===================== NAVBAR ===================== */
        function Navbar({ cartCount }) {
            const [menuOpen, setMenuOpen] = useState(false);
            return (
                <nav className="bg-white navbar-shadow sticky top-0 z-50">
                    <div className="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                        <a href="<?= base_url('home') ?>" className="text-2xl font-bold text-slate-800"
                            style={{ fontFamily: 'Playfair Display, serif' }}>
                            Shop<span className="text-blue-600">Ku</span>
                        </a>
                        <ul className="hidden md:flex gap-8 text-slate-600 font-medium">
                            <li><a href="<?= base_url('home') ?>"    className="hover:text-blue-600 transition">Home</a></li>
                            <li><a href="<?= base_url('katalog') ?>" className="hover:text-blue-600 transition">Katalog</a></li>
                            <li><a href="<?= base_url('login') ?>"   className="hover:text-blue-600 transition">Login</a></li>
                        </ul>
                        <div className="flex items-center gap-4">
                            <a href="<?= base_url('cart') ?>" className="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            <a href="<?= base_url('home') ?>">Home</a>
                            <a href="<?= base_url('katalog') ?>">Katalog</a>
                            <a href="<?= base_url('login') ?>">Login</a>
                        </div>
                    )}
                </nav>
            );
        }

        /* ===================== PAGE HEADER ===================== */
        function PageHeader({ count }) {
            return (
                <header className="hero-bg text-white py-10 px-4">
                    <div className="max-w-7xl mx-auto">
                        <p className="text-blue-300 text-sm font-medium mb-1">🛒 Keranjang Saya</p>
                        <h1 className="text-3xl md:text-4xl font-bold mb-1">Keranjang Belanja</h1>
                        <p className="text-slate-400 text-sm">{count} item dalam keranjang</p>
                    </div>
                </header>
            );
        }

        /* ===================== SKELETON ===================== */
        function SkeletonCartItem() {
            return (
                <div className="bg-white rounded-2xl p-5 flex gap-4 shadow-sm border border-slate-100">
                    <div className="skeleton w-24 h-24 rounded-xl flex-shrink-0"></div>
                    <div className="flex-1 space-y-3 py-1">
                        <div className="skeleton h-3 w-1/4 rounded"></div>
                        <div className="skeleton h-4 w-3/4 rounded"></div>
                        <div className="skeleton h-4 w-1/3 rounded"></div>
                        <div className="skeleton h-8 w-32 rounded-lg mt-2"></div>
                    </div>
                    <div className="skeleton w-20 h-6 rounded self-start mt-1"></div>
                </div>
            );
        }

        /* ===================== CART ITEM CARD ===================== */
        function CartItemCard({ item, onQtyChange, onRemove }) {
            const [removing, setRemoving] = useState(false);

            const handleRemove = () => {
                setRemoving(true);
                setTimeout(() => onRemove(item.id), 240);
            };

            return (
                <div className={`bg-white rounded-2xl p-5 flex gap-4 shadow-sm border border-slate-100 cart-item-enter ${removing ? 'cart-item-remove' : ''}`}>
                    {/* Gambar */}
                    <a href={`<?= base_url('detail') ?>/${item.id}`} className="flex-shrink-0">
                        <div className="w-24 h-24 bg-slate-50 rounded-xl flex items-center justify-center p-2">
                            <img src={item.image} alt={item.title}
                                className="max-h-full max-w-full object-contain" />
                        </div>
                    </a>

                    {/* Info */}
                    <div className="flex-1 min-w-0">
                        <p className="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-0.5">{item.category}</p>
                        <h3 className="text-slate-800 font-semibold text-sm leading-snug line-clamp-2 mb-2">{item.title}</h3>
                        <p className="text-blue-700 font-bold text-lg mb-3">${(item.price * item.qty).toFixed(2)}
                            {item.qty > 1 && <span className="text-slate-400 text-xs font-normal ml-1">(${item.price} × {item.qty})</span>}
                        </p>

                        {/* Qty control */}
                        <div className="flex items-center gap-3">
                            <div className="flex items-center border border-slate-200 rounded-xl overflow-hidden">
                                <button
                                    onClick={() => onQtyChange(item.id, item.qty - 1)}
                                    disabled={item.qty <= 1}
                                    className="px-3 py-1.5 text-slate-600 hover:bg-slate-100 transition font-bold disabled:opacity-30 disabled:cursor-not-allowed"
                                >−</button>
                                <span className="px-3 py-1.5 font-semibold text-slate-800 min-w-[32px] text-center text-sm">{item.qty}</span>
                                <button
                                    onClick={() => onQtyChange(item.id, item.qty + 1)}
                                    className="px-3 py-1.5 text-slate-600 hover:bg-slate-100 transition font-bold"
                                >+</button>
                            </div>
                            <button
                                onClick={handleRemove}
                                className="text-xs text-red-400 hover:text-red-600 font-medium transition flex items-center gap-1"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>

                    {/* Checkbox pilih */}
                    <div className="flex-shrink-0 pt-1">
                        <input type="checkbox" checked={item.selected}
                            onChange={() => {}}
                            onClick={() => onQtyChange(item.id, item.qty, !item.selected)}
                            className="w-4 h-4 accent-blue-600 cursor-pointer" />
                    </div>
                </div>
            );
        }

        /* ===================== ORDER SUMMARY ===================== */
        function OrderSummary({ items, onCheckout }) {
            const selected  = items.filter(i => i.selected);
            const subtotal  = selected.reduce((s, i) => s + i.price * i.qty, 0);
            const shipping  = subtotal > 0 && subtotal < 100 ? 9.99 : 0;
            const discount  = subtotal > 200 ? subtotal * 0.05 : 0;
            const total     = subtotal + shipping - discount;

            return (
                <aside className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 h-fit sticky top-24 space-y-4">
                    <h2 className="text-slate-800 font-bold text-lg">Ringkasan Pesanan</h2>

                    {/* Items terpilih */}
                    <div className="text-sm text-slate-500">
                        {selected.length > 0
                            ? <span>{selected.length} item dipilih</span>
                            : <span className="text-amber-500">⚠️ Belum ada item dipilih</span>}
                    </div>

                    <div className="border-t border-slate-100 pt-4 space-y-3 text-sm">
                        <div className="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span className="font-medium">${subtotal.toFixed(2)}</span>
                        </div>
                        <div className="flex justify-between text-slate-600">
                            <span>Ongkos Kirim</span>
                            {shipping === 0
                                ? <span className="text-green-600 font-medium">Gratis</span>
                                : <span className="font-medium">${shipping.toFixed(2)}</span>}
                        </div>
                        {discount > 0 && (
                            <div className="flex justify-between text-green-600">
                                <span>Diskon 5%</span>
                                <span className="font-medium">-${discount.toFixed(2)}</span>
                            </div>
                        )}
                    </div>

                    <div className="border-t border-slate-100 pt-4 flex justify-between font-bold text-slate-800 text-base">
                        <span>Total</span>
                        <span className="text-blue-600 text-xl">${total.toFixed(2)}</span>
                    </div>

                    {/* Promo info */}
                    {subtotal > 0 && subtotal < 100 && (
                        <div className="bg-blue-50 rounded-xl px-4 py-3 text-xs text-blue-700">
                            💡 Tambahkan <strong>${(100 - subtotal).toFixed(2)}</strong> lagi untuk gratis ongkir!
                        </div>
                    )}
                    {subtotal >= 100 && subtotal < 200 && (
                        <div className="bg-green-50 rounded-xl px-4 py-3 text-xs text-green-700">
                            ✅ Kamu sudah dapat gratis ongkir! Belanja <strong>${(200 - subtotal).toFixed(2)}</strong> lagi untuk diskon 5%.
                        </div>
                    )}

                    {/* Input kupon */}
                    <div className="flex gap-2 pt-1">
                        <input type="text" placeholder="Kode kupon"
                            className="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        <button className="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                            Pakai
                        </button>
                    </div>

                    <button
                        onClick={onCheckout}
                        disabled={selected.length === 0}
                        className="w-full btn-primary bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-xl transition text-center"
                    >
                        Checkout ({selected.length} item)
                    </button>

                    <a href="<?= base_url('katalog') ?>"
                        className="block text-center text-sm text-blue-600 hover:underline font-medium">
                        ← Lanjut Belanja
                    </a>

                    {/* Info jaminan */}
                    <div className="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-400">
                        <div className="flex items-center gap-2">🔒 <span>Pembayaran aman & terenkripsi</span></div>
                        <div className="flex items-center gap-2">↩️ <span>Garansi return 30 hari</span></div>
                        <div className="flex items-center gap-2">🚚 <span>Estimasi tiba 2-4 hari kerja</span></div>
                    </div>
                </aside>
            );
        }

        /* ===================== EMPTY STATE ===================== */
        function EmptyCart() {
            return (
                <div className="bg-white rounded-2xl p-16 text-center shadow-sm border border-slate-100">
                    <div className="text-7xl mb-4">🛒</div>
                    <h3 className="text-slate-700 font-bold text-xl mb-2">Keranjang Kosong</h3>
                    <p className="text-slate-400 text-sm mb-6">Yuk, mulai tambahkan produk ke keranjangmu!</p>
                    <a href="<?= base_url('katalog') ?>"
                        className="btn-primary inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                        Mulai Belanja →
                    </a>
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
                            <h3 className="text-white text-xl font-bold mb-3" style={{ fontFamily: 'Playfair Display, serif' }}>
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
                                <li>📍 AMIKOM YOGYAKARTA</li>
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
            const [cartItems, setCartItems] = useState([]);
            const [loading, setLoading]     = useState(true);
            const [error, setError]         = useState(null);
            const [toast, setToast]         = useState({ visible: false, message: '' });

            // ── useEffect: fetch 3 produk default dari Fake Store API ──
            useEffect(() => {
                const fetchProducts = async () => {
                    try {
                        const promises = DEFAULT_IDS.map(id =>
                            axios.get(`https://fakestoreapi.com/products/${id}`)
                        );
                        const results = await Promise.all(promises);
                        const items = results.map(res => ({
                            ...res.data,
                            qty: 1,
                            selected: true,
                        }));
                        setCartItems(items);
                    } catch (err) {
                        setError('Gagal memuat produk keranjang. Silakan coba lagi.');
                    } finally {
                        setLoading(false);
                    }
                };
                fetchProducts();
            }, []);

            // ── Computed values ──
            const totalQty = useMemo(
                () => cartItems.reduce((s, i) => s + i.qty, 0),
                [cartItems]
            );

            const allSelected = cartItems.length > 0 && cartItems.every(i => i.selected);

            // ── Handlers ──
            const showToast = (msg) => {
                setToast({ visible: true, message: msg });
                setTimeout(() => setToast({ visible: false, message: '' }), 2500);
            };

            // qty change; also handles checkbox toggle via 3rd param
            const handleQtyChange = (id, newQty, selectedOverride) => {
                setCartItems(prev =>
                    prev.map(item => {
                        if (item.id !== id) return item;
                        if (selectedOverride !== undefined)
                            return { ...item, selected: selectedOverride };
                        return { ...item, qty: Math.max(1, newQty) };
                    })
                );
            };

            const handleRemove = (id) => {
                const removed = cartItems.find(i => i.id === id);
                setCartItems(prev => prev.filter(i => i.id !== id));
                if (removed) showToast(`"${removed.title.substring(0, 25)}..." dihapus dari keranjang.`);
            };

            const handleSelectAll = () => {
                setCartItems(prev => prev.map(i => ({ ...i, selected: !allSelected })));
            };

            const handleClearAll = () => {
                setCartItems([]);
                showToast('Semua item dihapus dari keranjang.');
            };

            const handleCheckout = () => {
                const count = cartItems.filter(i => i.selected).length;
                showToast(`Memproses ${count} item... Menuju checkout!`);
                setTimeout(() => {
                    window.location.href = '<?= base_url('checkout') ?>';
                }, 1500);
            };

            return (
                <div>
                    <Navbar cartCount={totalQty} />
                    <PageHeader count={cartItems.length} />

                    <main className="max-w-7xl mx-auto px-4 py-8">

                        {/* Breadcrumb */}
                        <nav className="text-sm text-slate-400 mb-6 flex items-center gap-2">
                            <a href="<?= base_url('home') ?>"    className="hover:text-blue-600 transition">Home</a>
                            <span>›</span>
                            <a href="<?= base_url('katalog') ?>" className="hover:text-blue-600 transition">Katalog</a>
                            <span>›</span>
                            <span className="text-slate-600">Keranjang</span>
                        </nav>

                        {error && (
                            <div className="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 text-sm mb-6">
                                ⚠️ {error}
                            </div>
                        )}

                        {/* ── Layout grid ── */}
                        <div className="flex flex-col lg:flex-row gap-8 items-start">

                            {/* ── Left: Cart Items ── */}
                            <div className="flex-1 min-w-0 space-y-4">

                                {/* Toolbar pilih semua */}
                                {!loading && cartItems.length > 0 && (
                                    <div className="bg-white rounded-2xl px-5 py-3 flex items-center justify-between shadow-sm border border-slate-100">
                                        <label className="flex items-center gap-3 cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                checked={allSelected}
                                                onChange={handleSelectAll}
                                                className="w-4 h-4 accent-blue-600"
                                            />
                                            <span className="text-sm font-semibold text-slate-700">
                                                Pilih Semua ({cartItems.length} item)
                                            </span>
                                        </label>
                                        <button
                                            onClick={handleClearAll}
                                            className="text-xs text-red-400 hover:text-red-600 font-medium transition flex items-center gap-1"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Semua
                                        </button>
                                    </div>
                                )}

                                {/* Conditional rendering: loading / empty / items */}
                                {loading ? (
                                    <>
                                        <SkeletonCartItem />
                                        <SkeletonCartItem />
                                        <SkeletonCartItem />
                                    </>
                                ) : cartItems.length === 0 ? (
                                    <EmptyCart />
                                ) : (
                                    cartItems.map(item => (
                                        <CartItemCard
                                            key={item.id}
                                            item={item}
                                            onQtyChange={handleQtyChange}
                                            onRemove={handleRemove}
                                        />
                                    ))
                                )}

                                {/* Rekomendasi produk lain */}
                                {!loading && cartItems.length > 0 && (
                                    <div className="bg-blue-50 rounded-2xl px-5 py-4 border border-blue-100 text-sm text-blue-700 flex items-center justify-between">
                                        <span>🛍️ Mau tambah produk lain?</span>
                                        <a href="<?= base_url('katalog') ?>"
                                            className="font-semibold hover:underline">
                                            Lihat Katalog →
                                        </a>
                                    </div>
                                )}
                            </div>

                            {/* ── Right: Order Summary ── */}
                            <div className="w-full lg:w-80 flex-shrink-0">
                                {loading ? (
                                    <div className="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                                        <div className="skeleton h-5 w-1/2 rounded"></div>
                                        <div className="skeleton h-4 w-full rounded"></div>
                                        <div className="skeleton h-4 w-3/4 rounded"></div>
                                        <div className="skeleton h-10 w-full rounded-xl mt-2"></div>
                                    </div>
                                ) : (
                                    <OrderSummary items={cartItems} onCheckout={handleCheckout} />
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
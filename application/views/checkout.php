<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ShopKu - Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        .step-line {
            height: 2px;
            background: #e2e8f0;
            flex: 1;
            transition: background 0.4s;
        }

        .step-line.done {
            background: #2563eb;
        }

        .payment-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .payment-card:hover {
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .payment-card.selected {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .kurir-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .kurir-card:hover {
            border-color: #93c5fd;
        }

        .kurir-card.selected {
            border-color: #2563eb;
            background: #eff6ff;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeSlideIn 0.35s ease forwards;
        }

        @keyframes checkPop {
            0% {
                transform: scale(0);
            }

            70% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .check-pop {
            animation: checkPop 0.4s ease forwards;
        }
    </style>
</head>

<body class="bg-slate-50">
    <div id="root"></div>

    <script type="text/babel">
        const { useState, useEffect, useMemo } = React;

        /* ===================== NAVBAR ===================== */
        function Navbar() {
            return (
                <nav className="bg-white navbar-shadow sticky top-0 z-50">
                    <div className="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                        <a href="<?= base_url('home') ?>" className="text-2xl font-bold text-slate-800"
                            style={{ fontFamily: 'Playfair Display, serif' }}>
                            Shop<span className="text-blue-600">Ku</span>
                        </a>
                        <div className="flex items-center gap-2 text-sm text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Checkout Aman & Terenkripsi
                        </div>
                    </div>
                </nav>
            );
        }

        /* ===================== STEP INDICATOR ===================== */
        function StepIndicator({ currentStep }) {
            const steps = ['Pengiriman', 'Pembayaran', 'Konfirmasi'];
            return (
                <div className="max-w-2xl mx-auto px-4 py-8">
                    <div className="flex items-center gap-0">
                        {steps.map((label, i) => {
                            const stepNum = i + 1;
                            const isDone = currentStep > stepNum;
                            const isActive = currentStep === stepNum;
                            return (
                                <React.Fragment key={i}>
                                    <div className="flex flex-col items-center gap-1.5">
                                        <div className={`w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300
                                            ${isDone ? 'bg-blue-600 text-white' : isActive ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-slate-200 text-slate-400'}`}>
                                            {isDone
                                                ? <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 check-pop" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                                  </svg>
                                                : stepNum}
                                        </div>
                                        <span className={`text-xs font-semibold ${isActive ? 'text-blue-600' : isDone ? 'text-slate-600' : 'text-slate-400'}`}>
                                            {label}
                                        </span>
                                    </div>
                                    {i < steps.length - 1 && (
                                        <div className={`step-line mx-2 mb-5 ${isDone ? 'done' : ''}`}></div>
                                    )}
                                </React.Fragment>
                            );
                        })}
                    </div>
                </div>
            );
        }

        /* ===================== STEP 1: PENGIRIMAN ===================== */
        function StepPengiriman({ data, onChange, onNext }) {
            const [errors, setErrors] = useState({});

            const validate = () => {
                const e = {};
                if (!data.nama.trim())     e.nama     = 'Nama wajib diisi';
                if (!data.telepon.trim())  e.telepon  = 'Nomor telepon wajib diisi';
                if (!data.alamat.trim())   e.alamat   = 'Alamat wajib diisi';
                if (!data.kota.trim())     e.kota     = 'Kota wajib diisi';
                if (!data.kodepos.trim())  e.kodepos  = 'Kode pos wajib diisi';
                if (!data.kurir)           e.kurir    = 'Pilih kurir pengiriman';
                setErrors(e);
                return Object.keys(e).length === 0;
            };

            const kurirList = [
                { id: 'jne-reg',  label: 'JNE Regular',  estimasi: '2-3 hari', harga: 15000,  logo: '📦' },
                { id: 'jne-yes',  label: 'JNE YES',      estimasi: '1 hari',   harga: 35000,  logo: '⚡' },
                { id: 'sicepat',  label: 'SiCepat',      estimasi: '1-2 hari', harga: 20000,  logo: '🚀' },
                { id: 'jnt',      label: 'J&T Express',  estimasi: '2-3 hari', harga: 13000,  logo: '🚚' },
                { id: 'gosend',   label: 'GoSend Same Day', estimasi: 'Hari ini', harga: 50000, logo: '🛵' },
            ];

            const inputClass = (field) =>
                `w-full border rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 transition
                ${errors[field] ? 'border-red-300 focus:ring-red-300' : 'border-slate-200 focus:ring-blue-400'}`;

            return (
                <div className="fade-in space-y-6">
                    {/* Data Penerima */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 className="text-slate-800 font-bold text-base mb-5 flex items-center gap-2">
                            <span className="w-6 h-6 bg-blue-600 text-white rounded-full text-xs flex items-center justify-center font-bold">1</span>
                            Data Penerima
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div className="sm:col-span-2">
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Nama Lengkap *</label>
                                <input type="text" placeholder="Contoh: Budi Santoso"
                                    value={data.nama} onChange={e => onChange('nama', e.target.value)}
                                    className={inputClass('nama')} />
                                {errors.nama && <p className="text-red-500 text-xs mt-1">{errors.nama}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Nomor Telepon *</label>
                                <input type="tel" placeholder="08xxxxxxxxxx"
                                    value={data.telepon} onChange={e => onChange('telepon', e.target.value)}
                                    className={inputClass('telepon')} />
                                {errors.telepon && <p className="text-red-500 text-xs mt-1">{errors.telepon}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Email</label>
                                <input type="email" placeholder="email@contoh.com"
                                    value={data.email} onChange={e => onChange('email', e.target.value)}
                                    className={inputClass('email')} />
                            </div>
                            <div className="sm:col-span-2">
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Alamat Lengkap *</label>
                                <textarea placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan..."
                                    value={data.alamat} onChange={e => onChange('alamat', e.target.value)}
                                    rows={3} className={inputClass('alamat') + ' resize-none'} />
                                {errors.alamat && <p className="text-red-500 text-xs mt-1">{errors.alamat}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Kota / Kabupaten *</label>
                                <input type="text" placeholder="Contoh: Kudus"
                                    value={data.kota} onChange={e => onChange('kota', e.target.value)}
                                    className={inputClass('kota')} />
                                {errors.kota && <p className="text-red-500 text-xs mt-1">{errors.kota}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Kode Pos *</label>
                                <input type="text" placeholder="Contoh: 59311"
                                    value={data.kodepos} onChange={e => onChange('kodepos', e.target.value)}
                                    className={inputClass('kodepos')} />
                                {errors.kodepos && <p className="text-red-500 text-xs mt-1">{errors.kodepos}</p>}
                            </div>
                            <div className="sm:col-span-2">
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Catatan untuk Kurir</label>
                                <input type="text" placeholder="Contoh: Titip di pos satpam"
                                    value={data.catatan} onChange={e => onChange('catatan', e.target.value)}
                                    className={inputClass('catatan')} />
                            </div>
                        </div>
                    </div>

                    {/* Pilih Kurir */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 className="text-slate-800 font-bold text-base mb-5 flex items-center gap-2">
                            <span className="w-6 h-6 bg-blue-600 text-white rounded-full text-xs flex items-center justify-center font-bold">2</span>
                            Pilih Kurir Pengiriman
                        </h2>
                        <div className="space-y-3">
                            {kurirList.map(k => (
                                <div key={k.id} onClick={() => onChange('kurir', k.id)}
                                    className={`kurir-card border rounded-xl px-4 py-3 flex items-center justify-between ${data.kurir === k.id ? 'selected' : 'border-slate-200'}`}>
                                    <div className="flex items-center gap-3">
                                        <span className="text-xl">{k.logo}</span>
                                        <div>
                                            <p className="text-sm font-semibold text-slate-800">{k.label}</p>
                                            <p className="text-xs text-slate-400">Estimasi {k.estimasi}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <span className="text-sm font-bold text-slate-700">
                                            Rp {k.harga.toLocaleString('id-ID')}
                                        </span>
                                        <div className={`w-4 h-4 rounded-full border-2 flex items-center justify-center transition
                                            ${data.kurir === k.id ? 'border-blue-600 bg-blue-600' : 'border-slate-300'}`}>
                                            {data.kurir === k.id && (
                                                <div className="w-1.5 h-1.5 bg-white rounded-full"></div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                        {errors.kurir && <p className="text-red-500 text-xs mt-2">{errors.kurir}</p>}
                    </div>

                    <button onClick={() => { if (validate()) onNext(); }}
                        className="w-full btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl transition">
                        Lanjut ke Pembayaran →
                    </button>
                </div>
            );
        }

        /* ===================== STEP 2: PEMBAYARAN ===================== */
        function StepPembayaran({ data, onChange, onNext, onBack, orderTotal }) {
            const [errors, setErrors] = useState({});

            const validate = () => {
                const e = {};
                if (!data.metode) e.metode = 'Pilih metode pembayaran';
                if (data.metode === 'kartu') {
                    if (!data.kartuNomor.trim())  e.kartuNomor  = 'Nomor kartu wajib diisi';
                    if (!data.kartuNama.trim())   e.kartuNama   = 'Nama pemegang kartu wajib diisi';
                    if (!data.kartuExpiry.trim()) e.kartuExpiry = 'Masa berlaku wajib diisi';
                    if (!data.kartuCvv.trim())    e.kartuCvv    = 'CVV wajib diisi';
                }
                setErrors(e);
                return Object.keys(e).length === 0;
            };

            const metodeList = [
                {
                    id: 'transfer', label: 'Transfer Bank', icon: '🏦',
                    desc: 'BCA, Mandiri, BNI, BRI',
                    detail: (
                        <div className="mt-3 bg-blue-50 rounded-xl p-4 space-y-2 text-sm">
                            <p className="font-semibold text-slate-700 mb-2">Pilih Bank:</p>
                            {['BCA — 1234567890', 'Mandiri — 0987654321', 'BNI — 1122334455', 'BRI — 5566778899'].map(b => (
                                <label key={b} className="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="bank" value={b}
                                        checked={data.bank === b}
                                        onChange={() => onChange('bank', b)}
                                        className="accent-blue-600" />
                                    <span className="text-slate-700">{b}</span>
                                </label>
                            ))}
                            <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-2 text-xs text-yellow-700">
                                ⏰ Selesaikan pembayaran dalam <strong>24 jam</strong> setelah order dikonfirmasi.
                            </div>
                        </div>
                    )
                },
                {
                    id: 'cod', label: 'COD (Bayar di Tempat)', icon: '💵',
                    desc: 'Bayar saat paket tiba',
                    detail: (
                        <div className="mt-3 bg-green-50 rounded-xl p-4 text-sm text-green-700">
                            ✅ Kamu akan membayar <strong>Rp {orderTotal.toLocaleString('id-ID')}</strong> saat paket diterima. Siapkan uang pas ya!
                        </div>
                    )
                },
                {
                    id: 'ewallet', label: 'E-Wallet', icon: '📱',
                    desc: 'GoPay, OVO, DANA, ShopeePay',
                    detail: (
                        <div className="mt-3 bg-blue-50 rounded-xl p-4 space-y-2 text-sm">
                            <p className="font-semibold text-slate-700 mb-2">Pilih E-Wallet:</p>
                            {['GoPay', 'OVO', 'DANA', 'ShopeePay'].map(ew => (
                                <label key={ew} className="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="ewallet" value={ew}
                                        checked={data.ewallet === ew}
                                        onChange={() => onChange('ewallet', ew)}
                                        className="accent-blue-600" />
                                    <span className="text-slate-700">{ew}</span>
                                </label>
                            ))}
                        </div>
                    )
                },
                {
                    id: 'kartu', label: 'Kartu Kredit / Debit', icon: '💳',
                    desc: 'Visa, Mastercard, JCB',
                    detail: (
                        <div className="mt-3 space-y-3">
                            <div>
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Nomor Kartu *</label>
                                <input type="text" placeholder="1234 5678 9012 3456" maxLength={19}
                                    value={data.kartuNomor}
                                    onChange={e => {
                                        const v = e.target.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim();
                                        onChange('kartuNomor', v);
                                    }}
                                    className={`w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition
                                        ${errors.kartuNomor ? 'border-red-300 focus:ring-red-300' : 'border-slate-200 focus:ring-blue-400'}`} />
                                {errors.kartuNomor && <p className="text-red-500 text-xs mt-1">{errors.kartuNomor}</p>}
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-slate-500 mb-1.5">Nama Pemegang Kartu *</label>
                                <input type="text" placeholder="Sesuai kartu"
                                    value={data.kartuNama} onChange={e => onChange('kartuNama', e.target.value)}
                                    className={`w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition
                                        ${errors.kartuNama ? 'border-red-300 focus:ring-red-300' : 'border-slate-200 focus:ring-blue-400'}`} />
                                {errors.kartuNama && <p className="text-red-500 text-xs mt-1">{errors.kartuNama}</p>}
                            </div>
                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-500 mb-1.5">Masa Berlaku *</label>
                                    <input type="text" placeholder="MM/YY" maxLength={5}
                                        value={data.kartuExpiry}
                                        onChange={e => {
                                            let v = e.target.value.replace(/\D/g,'');
                                            if (v.length >= 2) v = v.slice(0,2) + '/' + v.slice(2);
                                            onChange('kartuExpiry', v);
                                        }}
                                        className={`w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition
                                            ${errors.kartuExpiry ? 'border-red-300 focus:ring-red-300' : 'border-slate-200 focus:ring-blue-400'}`} />
                                    {errors.kartuExpiry && <p className="text-red-500 text-xs mt-1">{errors.kartuExpiry}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs font-semibold text-slate-500 mb-1.5">CVV *</label>
                                    <input type="password" placeholder="•••" maxLength={4}
                                        value={data.kartuCvv} onChange={e => onChange('kartuCvv', e.target.value.replace(/\D/g,''))}
                                        className={`w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition
                                            ${errors.kartuCvv ? 'border-red-300 focus:ring-red-300' : 'border-slate-200 focus:ring-blue-400'}`} />
                                    {errors.kartuCvv && <p className="text-red-500 text-xs mt-1">{errors.kartuCvv}</p>}
                                </div>
                            </div>
                        </div>
                    )
                },
            ];

            return (
                <div className="fade-in space-y-6">
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h2 className="text-slate-800 font-bold text-base mb-5 flex items-center gap-2">
                            <span className="w-6 h-6 bg-blue-600 text-white rounded-full text-xs flex items-center justify-center font-bold">1</span>
                            Metode Pembayaran
                        </h2>
                        <div className="space-y-3">
                            {metodeList.map(m => (
                                <div key={m.id}>
                                    <div onClick={() => onChange('metode', m.id)}
                                        className={`payment-card border rounded-xl px-4 py-3 flex items-center justify-between ${data.metode === m.id ? 'selected' : 'border-slate-200'}`}>
                                        <div className="flex items-center gap-3">
                                            <span className="text-xl">{m.icon}</span>
                                            <div>
                                                <p className="text-sm font-semibold text-slate-800">{m.label}</p>
                                                <p className="text-xs text-slate-400">{m.desc}</p>
                                            </div>
                                        </div>
                                        <div className={`w-4 h-4 rounded-full border-2 flex items-center justify-center transition
                                            ${data.metode === m.id ? 'border-blue-600 bg-blue-600' : 'border-slate-300'}`}>
                                            {data.metode === m.id && <div className="w-1.5 h-1.5 bg-white rounded-full"></div>}
                                        </div>
                                    </div>
                                    {data.metode === m.id && m.detail}
                                </div>
                            ))}
                        </div>
                        {errors.metode && <p className="text-red-500 text-xs mt-2">{errors.metode}</p>}
                    </div>

                    <div className="flex gap-3">
                        <button onClick={onBack}
                            className="flex-1 border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold py-3.5 rounded-xl transition text-sm">
                            ← Kembali
                        </button>
                        <button onClick={() => { if (validate()) onNext(); }}
                            className="flex-[2] btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl transition text-sm">
                            Lanjut ke Konfirmasi →
                        </button>
                    </div>
                </div>
            );
        }

        /* ===================== STEP 3: KONFIRMASI ===================== */
        function StepKonfirmasi({ shipping, payment, orderSummary, onBack, onSubmit, loading }) {
            const kurirMap = {
                'jne-reg': { label: 'JNE Regular', harga: 15000 },
                'jne-yes': { label: 'JNE YES', harga: 35000 },
                'sicepat':  { label: 'SiCepat', harga: 20000 },
                'jnt':      { label: 'J&T Express', harga: 13000 },
                'gosend':   { label: 'GoSend Same Day', harga: 50000 },
            };
            const metodeMap = {
                transfer: 'Transfer Bank', cod: 'COD', ewallet: 'E-Wallet', kartu: 'Kartu Kredit/Debit'
            };
            const kurir = kurirMap[shipping.kurir] || { label: '-', harga: 0 };

            return (
                <div className="fade-in space-y-4">
                    {/* Ringkasan Pengiriman */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <div className="flex items-center justify-between mb-3">
                            <h3 className="font-bold text-slate-800 text-sm">Alamat Pengiriman</h3>
                            <button onClick={onBack} className="text-xs text-blue-600 hover:underline">Ubah</button>
                        </div>
                        <div className="text-sm text-slate-600 space-y-1">
                            <p className="font-semibold text-slate-800">{shipping.nama}</p>
                            <p>{shipping.telepon}</p>
                            <p className="text-slate-500">{shipping.alamat}, {shipping.kota} {shipping.kodepos}</p>
                            {shipping.catatan && <p className="text-slate-400 italic text-xs">Catatan: {shipping.catatan}</p>}
                        </div>
                        <div className="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-sm">
                            <span className="text-slate-500">Kurir</span>
                            <span className="font-semibold text-slate-700">{kurir.label} — Rp {kurir.harga.toLocaleString('id-ID')}</span>
                        </div>
                    </div>

                    {/* Ringkasan Pembayaran */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <h3 className="font-bold text-slate-800 text-sm mb-3">Metode Pembayaran</h3>
                        <p className="text-sm text-slate-600">{metodeMap[payment.metode] || '-'}
                            {payment.bank && ` — ${payment.bank}`}
                            {payment.ewallet && ` — ${payment.ewallet}`}
                            {payment.kartuNomor && ` — **** **** **** ${payment.kartuNomor.slice(-4)}`}
                        </p>
                    </div>

                    {/* Ringkasan Produk */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                        <h3 className="font-bold text-slate-800 text-sm mb-3">Ringkasan Pesanan</h3>
                        <div className="space-y-3 max-h-52 overflow-y-auto pr-1">
                            {orderSummary.items.map((item, i) => (
                                <div key={i} className="flex items-center gap-3">
                                    <div className="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center flex-shrink-0 p-1">
                                        <img src={item.image} alt={item.title} className="max-h-full max-w-full object-contain" />
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className="text-xs text-slate-700 font-medium line-clamp-1">{item.title}</p>
                                        <p className="text-xs text-slate-400">{item.qty}x ${item.price}</p>
                                    </div>
                                    <span className="text-sm font-bold text-slate-700">${(item.price * item.qty).toFixed(2)}</span>
                                </div>
                            ))}
                        </div>

                        <div className="border-t border-slate-100 mt-4 pt-4 space-y-2 text-sm">
                            <div className="flex justify-between text-slate-500">
                                <span>Subtotal</span>
                                <span>${orderSummary.subtotal.toFixed(2)}</span>
                            </div>
                            <div className="flex justify-between text-slate-500">
                                <span>Ongkos Kirim ({kurir.label})</span>
                                <span>Rp {kurir.harga.toLocaleString('id-ID')}</span>
                            </div>
                            {orderSummary.discount > 0 && (
                                <div className="flex justify-between text-green-600">
                                    <span>Diskon</span>
                                    <span>-${orderSummary.discount.toFixed(2)}</span>
                                </div>
                            )}
                            {orderSummary.promoDiscount > 0 && (
                                <div className="flex justify-between text-green-600">
                                    <span>Promo ({orderSummary.promoCode})</span>
                                    <span>-${orderSummary.promoDiscount.toFixed(2)}</span>
                                </div>
                            )}
                            <div className="flex justify-between font-bold text-slate-800 text-base pt-2 border-t border-slate-100">
                                <span>Total</span>
                                <span className="text-blue-600">${orderSummary.total.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    <div className="flex gap-3">
                        <button onClick={onBack}
                            className="flex-1 border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold py-3.5 rounded-xl transition text-sm">
                            ← Kembali
                        </button>
                        <button onClick={onSubmit} disabled={loading}
                            className="flex-[2] btn-primary bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-semibold py-3.5 rounded-xl transition text-sm flex items-center justify-center gap-2">
                            {loading
                                ? <><svg className="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                  </svg> Memproses...</>
                                : '✅ Konfirmasi & Bayar'}
                        </button>
                    </div>
                </div>
            );
        }

        /* ===================== SUCCESS ===================== */
        function OrderSuccess({ orderCode }) {
            return (
                <div className="fade-in max-w-md mx-auto text-center py-12 px-4">
                    <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5 check-pop">
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 className="text-2xl font-bold text-slate-800 mb-2">Pesanan Berhasil! 🎉</h2>
                    <p className="text-slate-500 text-sm mb-4">
                        Terima kasih telah berbelanja di ShopKu. Pesananmu sedang diproses.
                    </p>
                    <div className="bg-blue-50 rounded-xl px-5 py-4 inline-block mb-6">
                        <p className="text-xs text-blue-500 font-semibold mb-1">Kode Pesanan</p>
                        <p className="text-blue-700 font-bold text-lg tracking-widest">{orderCode}</p>
                    </div>
                    <div className="space-y-3 text-sm text-slate-500 mb-8">
                        <p>📧 Konfirmasi dikirim ke emailmu</p>
                        <p>🚚 Estimasi tiba 2–4 hari kerja</p>
                        <p>📱 Pantau status via nomor pesanan</p>
                    </div>
                    <div className="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="<?= base_url('home') ?>"
                            className="btn-primary bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                            Kembali ke Home
                        </a>
                        <a href="<?= base_url('katalog') ?>"
                            className="border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold px-8 py-3 rounded-xl transition">
                            Belanja Lagi
                        </a>
                    </div>
                </div>
            );
        }

        /* ===================== ORDER SIDEBAR ===================== */
        function OrderSidebar({ items, promoCode, onPromoCode, onApplyPromo, promoMsg, subtotal, discount, promoDiscount, ongkir, total }) {
            return (
                <aside className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 h-fit sticky top-24 space-y-4">
                    <h2 className="text-slate-800 font-bold text-base">Ringkasan Pesanan</h2>

                    {/* Produk List */}
                    <div className="space-y-3 max-h-48 overflow-y-auto pr-1">
                        {items.length === 0 ? (
                            <p className="text-slate-400 text-sm text-center py-4">Keranjang kosong</p>
                        ) : items.map((item, i) => (
                            <div key={i} className="flex items-center gap-3">
                                <div className="w-11 h-11 bg-slate-50 rounded-lg flex items-center justify-center flex-shrink-0 p-1">
                                    <img src={item.image} alt={item.title} className="max-h-full object-contain" />
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-xs text-slate-700 font-medium line-clamp-1">{item.title}</p>
                                    <p className="text-xs text-slate-400">{item.qty}x</p>
                                </div>
                                <span className="text-xs font-bold text-slate-700">${(item.price * item.qty).toFixed(2)}</span>
                            </div>
                        ))}
                    </div>

                    {/* Kode Promo */}
                    <div>
                        <label className="block text-xs font-semibold text-slate-500 mb-1.5">Kode Promo</label>
                        <div className="flex gap-2">
                            <input type="text" placeholder="SHOPKU10"
                                value={promoCode} onChange={e => onPromoCode(e.target.value.toUpperCase())}
                                className="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 uppercase" />
                            <button onClick={onApplyPromo}
                                className="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-3 py-2 rounded-lg transition">
                                Pakai
                            </button>
                        </div>
                        {promoMsg && (
                            <p className={`text-xs mt-1.5 font-medium ${promoMsg.ok ? 'text-green-600' : 'text-red-500'}`}>
                                {promoMsg.ok ? '✓ ' : '✕ '}{promoMsg.text}
                            </p>
                        )}
                    </div>

                    {/* Rincian Harga */}
                    <div className="border-t border-slate-100 pt-4 space-y-2 text-sm">
                        <div className="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span>${subtotal.toFixed(2)}</span>
                        </div>
                        <div className="flex justify-between text-slate-500">
                            <span>Ongkos Kirim</span>
                            {ongkir === 0
                                ? <span className="text-green-600 font-medium">Gratis</span>
                                : <span>Rp {ongkir.toLocaleString('id-ID')}</span>}
                        </div>
                        {discount > 0 && (
                            <div className="flex justify-between text-green-600">
                                <span>Diskon (5%)</span>
                                <span>-${discount.toFixed(2)}</span>
                            </div>
                        )}
                        {promoDiscount > 0 && (
                            <div className="flex justify-between text-green-600">
                                <span>Promo ({promoCode})</span>
                                <span>-${promoDiscount.toFixed(2)}</span>
                            </div>
                        )}
                    </div>
                    <div className="border-t border-slate-100 pt-3 flex justify-between font-bold text-slate-800">
                        <span>Total</span>
                        <span className="text-blue-600 text-lg">${total.toFixed(2)}</span>
                    </div>

                    <div className="space-y-1.5 text-xs text-slate-400 border-t border-slate-100 pt-3">
                        <div className="flex items-center gap-2">🔒 <span>Pembayaran aman & terenkripsi</span></div>
                        <div className="flex items-center gap-2">↩️ <span>Garansi return 30 hari</span></div>
                    </div>
                </aside>
            );
        }

        /* ===================== APP ROOT ===================== */
        function App() {
            const [step, setStep] = useState(1);
            const [done, setDone] = useState(false);
            const [orderCode, setOrderCode] = useState('');
            const [submitLoading, setSubmitLoading] = useState(false);

            // Data form
            const [shipping, setShipping] = useState({
                nama: '', telepon: '', email: '', alamat: '', kota: '', kodepos: '', catatan: '', kurir: ''
            });
            const [payment, setPayment] = useState({
                metode: '', bank: '', ewallet: '', kartuNomor: '', kartuNama: '', kartuExpiry: '', kartuCvv: ''
            });

            // Cart items dari Fake Store API
            const [cartItems, setCartItems] = useState([]);
            const [loadingCart, setLoadingCart] = useState(true);

            // Promo
            const [promoCode, setPromoCode] = useState('');
            const [promoMsg, setPromoMsg] = useState(null);
            const [promoDiscount, setPromoDiscount] = useState(0);

            const VALID_PROMOS = { 'SHOPKU10': 10, 'HEMAT20': 20, 'GRATIS': 15 };

            useEffect(() => {
                const ids = [1, 2, 3];
                Promise.all(ids.map(id => axios.get(`https://fakestoreapi.com/products/${id}`)))
                    .then(results => {
                        setCartItems(results.map(r => ({ ...r.data, qty: 1 })));
                        setLoadingCart(false);
                    })
                    .catch(() => setLoadingCart(false));
            }, []);

            const kurirHarga = {
                'jne-reg': 15000, 'jne-yes': 35000, 'sicepat': 20000, 'jnt': 13000, 'gosend': 50000
            };

            const subtotal = useMemo(() => cartItems.reduce((s, i) => s + i.price * i.qty, 0), [cartItems]);
            const discount = subtotal > 200 ? subtotal * 0.05 : 0;
            const ongkir = shipping.kurir ? (kurirHarga[shipping.kurir] || 0) : 0;
            const total = subtotal - discount - promoDiscount + (ongkir / 15000); // convert rough IDR to USD for display

            const handleApplyPromo = () => {
                const pct = VALID_PROMOS[promoCode];
                if (pct) {
                    const disc = subtotal * (pct / 100);
                    setPromoDiscount(disc);
                    setPromoMsg({ ok: true, text: `Promo ${promoCode} berhasil! Hemat $${disc.toFixed(2)}` });
                } else {
                    setPromoDiscount(0);
                    setPromoMsg({ ok: false, text: 'Kode promo tidak valid' });
                }
            };

            const handleSubmit = () => {
                setSubmitLoading(true);
                setTimeout(() => {
                    const code = 'SKU-' + Date.now().toString(36).toUpperCase();
                    setOrderCode(code);
                    setSubmitLoading(false);
                    setDone(true);
                }, 2000);
            };

            const orderSummary = {
                items: cartItems,
                subtotal,
                discount,
                promoDiscount,
                promoCode,
                total: subtotal - discount - promoDiscount,
            };

            if (done) return (
                <div>
                    <Navbar />
                    <OrderSuccess orderCode={orderCode} />
                </div>
            );

            return (
                <div>
                    <Navbar />

                    <header className="hero-bg text-white py-8 px-4">
                        <div className="max-w-7xl mx-auto">
                            <p className="text-blue-300 text-sm font-medium mb-1">💳 Checkout</p>
                            <h1 className="text-2xl md:text-3xl font-bold">Selesaikan Pesananmu</h1>
                        </div>
                    </header>

                    <StepIndicator currentStep={step} />

                    <main className="max-w-7xl mx-auto px-4 pb-16">
                        {/* Breadcrumb */}
                        <nav className="text-sm text-slate-400 mb-6 flex items-center gap-2">
                            <a href="<?= base_url('home') ?>" className="hover:text-blue-600 transition">Home</a>
                            <span>›</span>
                            <a href="<?= base_url('cart') ?>" className="hover:text-blue-600 transition">Keranjang</a>
                            <span>›</span>
                            <span className="text-slate-600">Checkout</span>
                        </nav>

                        <div className="flex flex-col lg:flex-row gap-8 items-start">
                            {/* LEFT: Form Steps */}
                            <div className="flex-1 min-w-0">
                                {step === 1 && (
                                    <StepPengiriman
                                        data={shipping}
                                        onChange={(k, v) => setShipping(p => ({ ...p, [k]: v }))}
                                        onNext={() => setStep(2)}
                                    />
                                )}
                                {step === 2 && (
                                    <StepPembayaran
                                        data={payment}
                                        onChange={(k, v) => setPayment(p => ({ ...p, [k]: v }))}
                                        onNext={() => setStep(3)}
                                        onBack={() => setStep(1)}
                                        orderTotal={Math.round(total * 15000)}
                                    />
                                )}
                                {step === 3 && (
                                    <StepKonfirmasi
                                        shipping={shipping}
                                        payment={payment}
                                        orderSummary={orderSummary}
                                        onBack={() => setStep(2)}
                                        onSubmit={handleSubmit}
                                        loading={submitLoading}
                                    />
                                )}
                            </div>

                            {/* RIGHT: Order Summary */}
                            <div className="w-full lg:w-80 flex-shrink-0">
                                {loadingCart ? (
                                    <div className="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                                        <div className="skeleton h-5 w-1/2 rounded"></div>
                                        {[1,2,3].map(i => <div key={i} className="skeleton h-12 w-full rounded-xl"></div>)}
                                        <div className="skeleton h-10 w-full rounded-xl"></div>
                                    </div>
                                ) : (
                                    <OrderSidebar
                                        items={cartItems}
                                        promoCode={promoCode}
                                        onPromoCode={setPromoCode}
                                        onApplyPromo={handleApplyPromo}
                                        promoMsg={promoMsg}
                                        subtotal={subtotal}
                                        discount={discount}
                                        promoDiscount={promoDiscount}
                                        ongkir={ongkir}
                                        total={subtotal - discount - promoDiscount}
                                    />
                                )}
                            </div>
                        </div>
                    </main>

                    <footer className="bg-slate-800 text-slate-300">
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
                                    <li><a href="<?= base_url('home') ?>" className="hover:text-white transition">Home</a></li>
                                    <li><a href="<?= base_url('katalog') ?>" className="hover:text-white transition">Katalog</a></li>
                                    <li><a href="<?= base_url('cart') ?>" className="hover:text-white transition">Keranjang</a></li>
                                    <li><a href="<?= base_url('login') ?>" className="hover:text-white transition">Login</a></li>
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
                </div>
            );
        }

        ReactDOM.render(<App />, document.getElementById('root'));
    </script>
</body>

</html>
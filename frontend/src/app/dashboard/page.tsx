'use client';

import React, { useEffect, useState } from 'react';

type StoredUser = {
  name?: string;
  email?: string;
  role?: string;
};

export default function DashboardPage() {
  const [user, setUser] = useState<StoredUser | null>(null);

  useEffect(() => {
    const storedUser = window.localStorage.getItem('medirecord_user');
    const token = window.localStorage.getItem('medirecord_token');

    if (!token) {
      window.location.href = '/#connexion';
      return;
    }

    if (storedUser) {
      setUser(JSON.parse(storedUser));
    }
  }, []);

  const handleLogout = () => {
    window.localStorage.removeItem('medirecord_token');
    window.localStorage.removeItem('medirecord_user');
    window.location.href = '/#connexion';
  };

  return (
    <main className="min-h-screen bg-[#F5F5F7] px-6 py-10">
      <div className="max-w-[1100px] mx-auto">
        <header className="flex items-center justify-between gap-4 mb-10">
          <a href="/" className="flex items-center gap-2">
            <div
              className="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold"
              style={{ background: '#E8192C' }}
            >
              +
            </div>
            <span className="font-display font-bold text-lg text-[#1D1D1F]">MediRecord</span>
          </a>

          <button type="button" onClick={handleLogout} className="btn-primary">
            Deconnexion
          </button>
        </header>

        <section className="rounded-3xl bg-white p-8 shadow-[0_20px_60px_rgba(0,0,0,0.08)]">
          <p className="section-eyebrow mb-3">Tableau de bord</p>
          <h1 className="font-display font-black text-[#1D1D1F] text-4xl mb-4">
            Bienvenue{user?.name ? `, ${user.name}` : ''}.
          </h1>
          <p className="text-[#6E6E73] max-w-2xl leading-relaxed">
            Votre compte est connecte au backend Laravel. Les prochains modules peuvent maintenant
            utiliser les API protegees: patients, medecins, consultations, dossiers, medicaments et
            ordonnances.
          </p>

          <div className="grid sm:grid-cols-3 gap-4 mt-8">
            <div className="rounded-2xl bg-[#F5F5F7] p-5">
              <p className="text-sm text-[#6E6E73] mb-1">Email</p>
              <p className="font-semibold text-[#1D1D1F] break-words">{user?.email || '-'}</p>
            </div>
            <div className="rounded-2xl bg-[#F5F5F7] p-5">
              <p className="text-sm text-[#6E6E73] mb-1">Role</p>
              <p className="font-semibold text-[#1D1D1F]">{user?.role || '-'}</p>
            </div>
            <div className="rounded-2xl bg-[#F5F5F7] p-5">
              <p className="text-sm text-[#6E6E73] mb-1">Statut</p>
              <p className="font-semibold text-[#1D1D1F]">Connecte</p>
            </div>
          </div>
        </section>
      </div>
    </main>
  );
}

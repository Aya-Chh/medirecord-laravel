'use client';

import React from 'react';

const links = [
  { label: 'Fonctionnalités', href: '#fonctionnalites' },
  { label: 'Sécurité', href: '#securite' },
  { label: 'Médecins', href: '#roles' },
  { label: 'Contact', href: 'mailto:contact@medirecord.ma' },
];

export default function Footer() {
  return (
    <footer
      className="py-12"
      style={{
        background: '#F5F5F7',
        borderTop: '1px solid rgba(0,0,0,0.08)',
      }}
    >
      <div className="max-w-[1200px] mx-auto px-6">
        <div className="flex flex-col md:flex-row items-center justify-between gap-6">
          {/* Logo */}
          <div className="flex items-center gap-2">
            <div
              className="w-6 h-6 rounded-md flex items-center justify-center text-white text-xs font-bold"
              style={{ background: '#E8192C' }}
            >
              +
            </div>
            <span
              className="font-display font-bold"
              style={{ fontSize: '0.9375rem', color: '#1D1D1F', letterSpacing: '-0.02em' }}
            >
              MediRecord
            </span>
          </div>

          {/* Nav links */}
          <nav className="flex flex-wrap justify-center gap-x-7 gap-y-2">
            {links?.map((link) => (
              <a
                key={link?.href}
                href={link?.href}
                className="transition-colors duration-200"
                style={{ fontSize: '0.8125rem', color: '#6E6E73' }}
                onMouseEnter={(e) => (e.currentTarget.style.color = '#1D1D1F')}
                onMouseLeave={(e) => (e.currentTarget.style.color = '#6E6E73')}
              >
                {link?.label}
              </a>
            ))}
          </nav>

          {/* Legal */}
          <div
            className="flex items-center gap-4"
            style={{ fontSize: '0.8125rem', color: '#6E6E73' }}
          >
            <a
              href="#"
              className="transition-colors duration-200"
              onMouseEnter={(e) => (e.currentTarget.style.color = '#1D1D1F')}
              onMouseLeave={(e) => (e.currentTarget.style.color = '#6E6E73')}
            >
              Confidentialité
            </a>
            <span style={{ opacity: 0.3 }}>·</span>
            <a
              href="#"
              className="transition-colors duration-200"
              onMouseEnter={(e) => (e.currentTarget.style.color = '#1D1D1F')}
              onMouseLeave={(e) => (e.currentTarget.style.color = '#6E6E73')}
            >
              Conditions
            </a>
            <span style={{ opacity: 0.3 }}>·</span>
            <span>© 2026 MediRecord</span>
          </div>
        </div>

        <div
          className="mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3"
          style={{ borderTop: '1px solid rgba(0,0,0,0.06)' }}
        >
          <p style={{ fontSize: '0.75rem', color: '#6E6E73', opacity: 0.7 }}>
            Conforme à la loi 09-08 sur la protection des données personnelles · Maroc
          </p>
          <div className="flex items-center gap-2">
            <span
              className="w-1.5 h-1.5 rounded-full"
              style={{ background: '#34C759', boxShadow: '0 0 6px rgba(52,199,89,0.6)' }}
            />
            <span style={{ fontSize: '0.75rem', color: '#6E6E73', opacity: 0.7 }}>
              Tous les systèmes opérationnels
            </span>
          </div>
        </div>
      </div>
    </footer>
  );
}

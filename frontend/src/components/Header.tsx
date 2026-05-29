'use client';

import React, { useEffect, useState } from 'react';

const navLinks = [
  { label: 'Fonctionnalités', href: '#fonctionnalites' },
  { label: 'Sécurité', href: '#securite' },
  { label: 'Rôles', href: '#roles' },
  { label: 'Comment ça marche', href: '#comment' },
  { label: 'Connexion', href: '#connexion' },
  { label: 'Inscription', href: '#inscription' },
];

export default function Header() {
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <>
      <header
        className="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
        style={{
          background: scrolled ? 'rgba(245,245,247,0.85)' : 'transparent',
          backdropFilter: scrolled ? 'saturate(180%) blur(20px)' : 'none',
          WebkitBackdropFilter: scrolled ? 'saturate(180%) blur(20px)' : 'none',
          borderBottom: scrolled ? '1px solid rgba(0,0,0,0.08)' : '1px solid transparent',
        }}
      >
        <div className="max-w-[1200px] mx-auto px-6 h-14 flex items-center justify-between">
          {/* Logo */}
          <a
            href="#"
            onClick={(e) => {
              e?.preventDefault();
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }}
            className="flex items-center gap-2 group"
          >
            <div
              className="w-7 h-7 rounded-lg flex items-center justify-center text-white text-sm font-bold transition-transform duration-300 group-hover:scale-110"
              style={{ background: '#E8192C' }}
            >
              +
            </div>
            <span
              className="font-display font-bold text-base tracking-tight"
              style={{ color: '#1D1D1F', letterSpacing: '-0.02em' }}
            >
              MediRecord
            </span>
          </a>

          {/* Desktop Nav */}
          <nav className="hidden lg:flex items-center gap-8">
            {navLinks?.map((link) => (
              <a key={link?.href} href={link?.href} className="nav-link">
                {link?.label}
              </a>
            ))}
          </nav>

          {/* CTA */}
          <div className="hidden lg:flex items-center gap-3">
            <a
              href="#connexion"
              className="text-sm font-medium transition-colors duration-200"
              style={{ color: '#1D1D1F', letterSpacing: '-0.01em' }}
              onMouseEnter={(e) => (e.currentTarget.style.color = '#E8192C')}
              onMouseLeave={(e) => (e.currentTarget.style.color = '#1D1D1F')}
            >
              Connexion
            </a>
            <a
              href="#inscription"
              className="btn-primary"
              style={{ fontSize: '0.8125rem', padding: '0.5rem 1.25rem' }}
            >
              Commencer
            </a>
          </div>

          {/* Mobile hamburger */}
          <button
            className="lg:hidden flex flex-col gap-1.5 p-2"
            onClick={() => setMenuOpen(!menuOpen)}
            aria-label="Menu"
          >
            <span
              className="block h-0.5 transition-all duration-300"
              style={{
                width: '22px',
                background: '#1D1D1F',
                transform: menuOpen ? 'rotate(45deg) translate(3px, 3px)' : 'none',
              }}
            />
            <span
              className="block h-0.5 transition-all duration-300"
              style={{
                width: '16px',
                background: '#1D1D1F',
                opacity: menuOpen ? 0 : 1,
              }}
            />
            <span
              className="block h-0.5 transition-all duration-300"
              style={{
                width: '22px',
                background: '#1D1D1F',
                transform: menuOpen ? 'rotate(-45deg) translate(3px, -3px)' : 'none',
              }}
            />
          </button>
        </div>
      </header>
      {/* Mobile Menu */}
      {menuOpen && (
        <div
          className="fixed inset-0 z-40 flex flex-col items-center justify-center gap-8 lg:hidden"
          style={{ background: 'rgba(245,245,247,0.97)', backdropFilter: 'blur(20px)' }}
        >
          <button
            className="absolute top-5 right-6 text-2xl"
            style={{ color: '#1D1D1F' }}
            onClick={() => setMenuOpen(false)}
            aria-label="Fermer"
          >
            ✕
          </button>
          {navLinks?.map((link) => (
            <a
              key={link?.href}
              href={link?.href}
              className="font-display text-2xl font-bold transition-colors duration-200"
              style={{ color: '#1D1D1F' }}
              onClick={() => setMenuOpen(false)}
              onMouseEnter={(e) => (e.currentTarget.style.color = '#E8192C')}
              onMouseLeave={(e) => (e.currentTarget.style.color = '#1D1D1F')}
            >
              {link?.label}
            </a>
          ))}
          <a href="#inscription" className="btn-primary mt-4" onClick={() => setMenuOpen(false)}>
            Commencer
          </a>
        </div>
      )}
    </>
  );
}

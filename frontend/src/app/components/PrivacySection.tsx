'use client';

import React, { useEffect, useRef } from 'react';
import AppImage from '@/components/ui/AppImage';

const privacyPoints = [
  {
    title: 'Chiffrement AES-256',
    desc: 'Toutes vos données sont chiffrées en transit et au repos avec le standard militaire AES-256.',
    icon: '🔒',
  },
  {
    title: 'Conformité CNDP',
    desc: 'Entièrement conforme à la loi 09-08 sur la protection des données personnelles au Maroc.',
    icon: '✅',
  },
  {
    title: 'Accès contrôlé',
    desc: 'Vous décidez qui peut voir quoi. Chaque accès est journalisé et auditable à tout moment.',
    icon: '👁',
  },
  {
    title: 'Hébergement local',
    desc: 'Données hébergées sur des serveurs certifiés au Maroc. Aucun transfert hors frontières.',
    icon: '🇲🇦',
  },
];

export default function PrivacySection() {
  const sectionRef = useRef<HTMLElement>(null);
  const imageRef = useRef<HTMLDivElement>(null);
  const contentRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const cards = sectionRef.current?.querySelectorAll<HTMLElement>('[data-reveal]');
    if (!cards) return;
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const el = entry.target as HTMLElement;
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
          }
        });
      },
      { threshold: 0.1 }
    );
    cards.forEach((c) => observer.observe(c));
    return () => observer.disconnect();
  }, []);

  useEffect(() => {
    const section = sectionRef.current;
    const image = imageRef.current;
    if (!section || !image) return;
    const onScroll = () => {
      const rect = section.getBoundingClientRect();
      const vh = window.innerHeight;
      const progress = 1 - (rect.top + rect.height / 2) / (vh + rect.height / 2);
      const clamped = Math.max(0, Math.min(1, progress));
      image.style.transform = `translateY(${(0.5 - clamped) * 30}px) scale(1.04)`;
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <section
      id="securite"
      ref={sectionRef}
      className="py-28 relative overflow-hidden"
      style={{ background: '#FFFFFF' }}
    >
      <div className="max-w-[1200px] mx-auto px-6">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          {/* Left: HD Image */}
          <div
            ref={imageRef}
            className="relative"
            style={{ willChange: 'transform', transform: 'scale(1.04)' }}
          >
            <div
              className="relative rounded-3xl overflow-hidden"
              style={{
                aspectRatio: '4/5',
                boxShadow: '0 40px 80px rgba(0,0,0,0.18)',
              }}
            >
              <AppImage
                src="/assets/images/im5-1778497644375.jpg"
                alt="Réseau vasculaire rouge complexe en macro photographie, vaisseaux sanguins entrelacés sur fond sombre"
                fill
                className="object-cover"
                sizes="(max-width: 1024px) 100vw, 50vw"
              />
              {/* Subtle gradient */}
              <div
                className="absolute inset-0"
                style={{
                  background:
                    'linear-gradient(135deg, rgba(0,0,0,0.1) 0%, transparent 60%, rgba(0,0,0,0.2) 100%)',
                }}
              />
              {/* Floating badge */}
              <div
                className="absolute bottom-8 left-6 right-6 rounded-2xl p-5"
                style={{
                  background: 'rgba(255,255,255,0.92)',
                  backdropFilter: 'blur(20px)',
                  boxShadow: '0 8px 32px rgba(0,0,0,0.12)',
                }}
              >
                <div className="flex items-center gap-3 mb-3">
                  <div
                    className="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                    style={{ background: 'rgba(232,25,44,0.1)' }}
                  >
                    🛡️
                  </div>
                  <span
                    className="font-display font-bold"
                    style={{ fontSize: '0.9375rem', color: '#1D1D1F' }}
                  >
                    Sécurité Certifiée
                  </span>
                </div>
                <div className="flex gap-2 flex-wrap">
                  {['ISO 27001', 'CNDP', 'TLS 1.3', 'AES-256'].map((badge) => (
                    <span
                      key={badge}
                      className="px-2.5 py-1 rounded-full text-xs font-semibold"
                      style={{
                        background: 'rgba(232,25,44,0.08)',
                        color: '#E8192C',
                        border: '1px solid rgba(232,25,44,0.2)',
                      }}
                    >
                      {badge}
                    </span>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {/* Right: Content */}
          <div ref={contentRef} className="flex flex-col gap-10">
            <div
              data-reveal
              style={{
                opacity: 0,
                transform: 'translateY(24px)',
                transition:
                  'opacity 0.8s cubic-bezier(0.23,1,0.32,1), transform 0.8s cubic-bezier(0.23,1,0.32,1)',
              }}
            >
              <p className="section-eyebrow mb-4">Sécurité & Confidentialité</p>
              <h2
                className="font-display font-black"
                style={{
                  fontSize: 'clamp(2.25rem, 4vw, 4rem)',
                  color: '#1D1D1F',
                  letterSpacing: '-0.04em',
                  lineHeight: 1.05,
                }}
              >
                Vos données,
                <br />
                votre contrôle.
              </h2>
              <p
                className="mt-5 leading-relaxed"
                style={{ fontSize: '1.0625rem', color: '#6E6E73', lineHeight: 1.65 }}
              >
                MediRecord a été conçu avec la confidentialité comme principe fondamental. Nous ne
                vendons jamais vos données. Vous restez propriétaire de votre dossier médical.
              </p>
            </div>

            <div className="flex flex-col gap-5">
              {privacyPoints.map((point, i) => (
                <div
                  key={point.title}
                  data-reveal
                  className="flex items-start gap-4"
                  style={{
                    opacity: 0,
                    transform: 'translateY(20px)',
                    transition: `opacity 0.7s cubic-bezier(0.23,1,0.32,1) ${i * 80}ms, transform 0.7s cubic-bezier(0.23,1,0.32,1) ${i * 80}ms`,
                  }}
                >
                  <div
                    className="w-11 h-11 shrink-0 rounded-2xl flex items-center justify-center text-lg"
                    style={{ background: '#F5F5F7' }}
                  >
                    {point.icon}
                  </div>
                  <div>
                    <h4
                      className="font-display font-bold mb-1"
                      style={{ fontSize: '1rem', color: '#1D1D1F', letterSpacing: '-0.01em' }}
                    >
                      {point.title}
                    </h4>
                    <p style={{ fontSize: '0.9375rem', color: '#6E6E73', lineHeight: 1.6 }}>
                      {point.desc}
                    </p>
                  </div>
                </div>
              ))}
            </div>

            <div
              data-reveal
              style={{
                opacity: 0,
                transform: 'translateY(20px)',
                transition:
                  'opacity 0.7s cubic-bezier(0.23,1,0.32,1) 0.4s, transform 0.7s cubic-bezier(0.23,1,0.32,1) 0.4s',
              }}
            >
              <a href="#inscription" className="btn-primary self-start" style={{ display: 'inline-flex' }}>
                En savoir plus sur la sécurité
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

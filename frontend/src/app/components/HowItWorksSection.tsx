'use client';

import React, { useEffect, useRef } from 'react';

const steps = [
  {
    num: '01',
    title: 'Créez votre compte',
    desc: "Inscrivez-vous en 2 minutes avec votre CIN ou votre numéro de praticien. Vérification d'identité sécurisée.",
    detail: 'Gratuit pour les patients',
  },
  {
    num: '02',
    title: 'Importez vos données',
    desc: 'Téléversez vos documents existants ou laissez vos médecins alimenter directement votre dossier.',
    detail: 'Formats PDF, DICOM, JPEG',
  },
  {
    num: '03',
    title: 'Partagez en sécurité',
    desc: "Accordez l'accès à vos médecins et spécialistes pour une durée définie. Révoquez à tout moment.",
    detail: 'Traçabilité complète',
  },
  {
    num: '04',
    title: 'Suivez votre santé',
    desc: "Visualisez l'évolution de vos données médicales et recevez des rappels personnalisés.",
    detail: 'Alertes intelligentes',
  },
];

export default function HowItWorksSection() {
  const sectionRef = useRef<HTMLElement>(null);

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

  return (
    <section id="comment" ref={sectionRef} className="py-28" style={{ background: '#FFFFFF' }}>
      <div className="max-w-[1200px] mx-auto px-6">
        {/* Header */}
        <div className="text-center mb-20">
          <p className="section-eyebrow mb-3">Processus</p>
          <h2
            className="font-display font-black"
            style={{
              fontSize: 'clamp(2.5rem, 5vw, 4.5rem)',
              color: '#1D1D1F',
              letterSpacing: '-0.04em',
              lineHeight: 1.05,
            }}
          >
            Démarrez en
            <br />4 étapes simples.
          </h2>
          <p
            className="mt-4 max-w-lg mx-auto"
            style={{ fontSize: '1.0625rem', color: '#6E6E73', lineHeight: 1.6 }}
          >
            De l&apos;inscription à votre premier dossier complet — moins de 15 minutes.
          </p>
        </div>

        {/* Steps grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
          {/* Connecting line */}
          <div
            className="absolute top-10 left-[12.5%] right-[12.5%] h-px hidden lg:block pointer-events-none"
            style={{
              background:
                'linear-gradient(to right, transparent, rgba(232,25,44,0.2) 20%, rgba(232,25,44,0.2) 80%, transparent)',
            }}
          />

          {steps.map((step, i) => (
            <div
              key={step.num}
              data-reveal
              className="flex flex-col gap-5"
              style={{
                opacity: 0,
                transform: 'translateY(24px)',
                transition: `opacity 0.8s cubic-bezier(0.23,1,0.32,1) ${i * 120}ms, transform 0.8s cubic-bezier(0.23,1,0.32,1) ${i * 120}ms`,
              }}
            >
              {/* Step number circle */}
              <div className="flex items-center justify-center">
                <div
                  className="w-20 h-20 rounded-full flex flex-col items-center justify-center relative z-10"
                  style={{
                    background: '#FFFFFF',
                    border: '1.5px solid rgba(232,25,44,0.2)',
                    boxShadow: '0 4px 20px rgba(0,0,0,0.06)',
                  }}
                >
                  <span
                    className="font-display font-black"
                    style={{ fontSize: '1.5rem', color: '#E8192C', lineHeight: 1 }}
                  >
                    {step.num}
                  </span>
                </div>
              </div>

              {/* Card */}
              <div
                className="rounded-2xl p-6 flex flex-col gap-3 flex-1"
                style={{
                  background: '#F5F5F7',
                }}
              >
                <h4
                  className="font-display font-bold"
                  style={{ fontSize: '1.0625rem', color: '#1D1D1F', letterSpacing: '-0.02em' }}
                >
                  {step.title}
                </h4>
                <p style={{ fontSize: '0.9375rem', color: '#6E6E73', lineHeight: 1.6 }}>
                  {step.desc}
                </p>
                <div className="mt-auto pt-3" style={{ borderTop: '1px solid rgba(0,0,0,0.08)' }}>
                  <span
                    className="font-semibold"
                    style={{ fontSize: '0.75rem', color: '#E8192C', letterSpacing: '0.04em' }}
                  >
                    {step.detail}
                  </span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

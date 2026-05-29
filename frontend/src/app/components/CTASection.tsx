'use client';

import React, { useEffect, useRef, useState } from 'react';
import AppImage from '@/components/ui/AppImage';

export default function CTASection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) setVisible(true);
      },
      { threshold: 0.2 }
    );

    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  return (
    <section
      id="cta"
      ref={sectionRef}
      className="relative overflow-hidden"
      style={{ background: '#000000' }}
    >
      <div className="absolute inset-0 z-0">
        <AppImage
          src="/assets/images/im3-1778497653022.jpg"
          alt="Trace electrocardiogramme lumineux rouge sur fond sombre"
          fill
          className="object-cover opacity-40"
          sizes="100vw"
        />
        <div
          className="absolute inset-0"
          style={{
            background:
              'linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.8) 100%)',
          }}
        />
      </div>

      <div className="relative z-10 max-w-[800px] mx-auto px-6 py-32 text-center">
        <p className="section-label mb-6 block" style={{ color: 'rgba(255,255,255,0.6)' }}>
          Acces Anticipe
        </p>

        <h2
          className="font-display font-black text-white mb-6"
          style={{
            fontSize: 'clamp(2.5rem, 6vw, 5.5rem)',
            letterSpacing: '-0.04em',
            lineHeight: 1.0,
            opacity: visible ? 1 : 0,
            transform: visible ? 'translateY(0)' : 'translateY(30px)',
            transition:
              'opacity 0.9s cubic-bezier(0.23,1,0.32,1) 0.1s, transform 0.9s cubic-bezier(0.23,1,0.32,1) 0.1s',
          }}
        >
          Rejoignez les 50 000+
          <br />
          <span style={{ color: '#FF4D5E' }}>utilisateurs MediRecord.</span>
        </h2>

        <p
          className="mb-10 max-w-lg mx-auto"
          style={{
            fontSize: '1.125rem',
            color: 'rgba(255,255,255,0.65)',
            lineHeight: 1.65,
            opacity: visible ? 1 : 0,
            transform: visible ? 'translateY(0)' : 'translateY(20px)',
            transition:
              'opacity 0.9s cubic-bezier(0.23,1,0.32,1) 0.25s, transform 0.9s cubic-bezier(0.23,1,0.32,1) 0.25s',
          }}
        >
          Creez votre dossier medical numerique gratuitement. Acces immediat, aucune carte de
          credit requise.
        </p>

        <div
          style={{
            opacity: visible ? 1 : 0,
            transform: visible ? 'translateY(0)' : 'translateY(20px)',
            transition:
              'opacity 0.9s cubic-bezier(0.23,1,0.32,1) 0.4s, transform 0.9s cubic-bezier(0.23,1,0.32,1) 0.4s',
          }}
        >
          <a href="#inscription" className="btn-primary inline-flex">
            Creer mon compte
          </a>

          <p className="mt-5 text-xs" style={{ color: 'rgba(255,255,255,0.35)' }}>
            Gratuit pour les patients - Essai 30 jours pour les medecins - Aucune CB requise
          </p>
        </div>
      </div>
    </section>
  );
}

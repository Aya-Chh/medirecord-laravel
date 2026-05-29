'use client';

import React, { useEffect, useRef } from 'react';
import AppImage from '@/components/ui/AppImage';

interface Feature {
  title: string;
  desc: string;
  tag?: string;
  image?: string;
  imageAlt?: string;
  span?: string;
  dark?: boolean;
}

const features: Feature[] = [
  {
    title: 'Dossier Médical Unifié',
    desc: "Regroupez ordonnances, résultats d'analyses, comptes-rendus et imagerie dans un seul espace structuré et chronologique.",
    tag: 'Cœur du système',
    image: '/assets/images/im66-1778497644150.jpg',
    imageAlt: 'Scanner IRM cérébral montrant coupes axiales lumineuses haute résolution',
    span: 'lg:col-span-2 lg:row-span-2',
    dark: true,
  },
  {
    title: 'Ordonnances Digitales',
    desc: 'Génération et transmission sécurisée des ordonnances directement aux pharmacies. Zéro papier.',
    tag: 'Nouveau',
    image: '/assets/images/im2-1778497653011.jpg',
    imageAlt:
      'Réseau de vaisseaux sanguins rouges en macro, structure vasculaire illuminée sur fond sombre',
    dark: true,
  },
  {
    title: 'Imagerie Médicale',
    desc: 'Stockage et visualisation DICOM intégré. Partagez vos IRM et radiographies en un clic.',
    tag: 'DICOM',
    image: '/assets/images/im6-1778497644153.webp',
    imageAlt:
      'Anatomie humaine transparente révélant les organes internes, illustration médicale 3D premium',
    dark: true,
  },
  {
    title: 'Accès Urgence',
    desc: "Code QR médical d'urgence donnant accès aux informations vitales : groupe sanguin, allergies, traitements.",
    tag: 'Vital',
    image: '/assets/images/im66-1778497644150.jpg',
    imageAlt: 'Scanner médical haute résolution, imagerie diagnostique avancée',
    dark: true,
  },
  {
    title: 'Suivi & Analytics',
    desc: "Visualisez l'évolution de vos paramètres de santé sur des graphiques interactifs.",
    image: '/assets/images/im3-1778497653022.jpg',
    imageAlt: 'Tracé EKG électrocardiogramme rouge lumineux sur fond sombre, monitoring cardiaque',
    span: 'lg:col-span-2',
    dark: true,
  },
];

export default function FeaturesSection() {
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
      { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
    );
    cards.forEach((card) => observer.observe(card));
    return () => observer.disconnect();
  }, []);

  return (
    <section
      id="fonctionnalites"
      ref={sectionRef}
      className="py-28"
      style={{ background: '#F5F5F7' }}
    >
      <div className="max-w-[1200px] mx-auto px-6">
        {/* Header */}
        <div className="text-center mb-16">
          <p className="section-eyebrow mb-3">Fonctionnalités</p>
          <h2
            className="font-display font-black"
            style={{
              fontSize: 'clamp(2.5rem, 5vw, 4.5rem)',
              color: '#1D1D1F',
              letterSpacing: '-0.04em',
              lineHeight: 1.05,
            }}
          >
            Tout ce dont vous
            <br />
            avez besoin.
          </h2>
          <p
            className="mt-4 max-w-lg mx-auto"
            style={{ fontSize: '1.0625rem', color: '#6E6E73', lineHeight: 1.6 }}
          >
            Une suite complète d&apos;outils conçus pour simplifier la gestion médicale au
            quotidien.
          </p>
        </div>

        {/* Bento Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 auto-rows-auto">
          {features.map((feature, i) => (
            <div
              key={feature.title}
              data-reveal
              className={`relative overflow-hidden rounded-3xl group ${feature.span || ''}`}
              style={{
                opacity: 0,
                transform: 'translateY(24px)',
                transition: `opacity 0.7s cubic-bezier(0.23,1,0.32,1) ${i * 80}ms, transform 0.7s cubic-bezier(0.23,1,0.32,1) ${i * 80}ms`,
                background: feature.dark ? '#1D1D1F' : '#FFFFFF',
                minHeight: feature.image ? '380px' : '220px',
                boxShadow: '0 2px 20px rgba(0,0,0,0.06)',
              }}
            >
              {/* Background image */}
              {feature.image && (
                <div className="absolute inset-0 z-0">
                  <AppImage
                    src={feature.image}
                    alt={feature.imageAlt || feature.title}
                    fill
                    className="object-cover transition-transform duration-700 group-hover:scale-105"
                    sizes="(max-width: 768px) 100vw, 50vw"
                  />
                  <div
                    className="absolute inset-0"
                    style={{
                      background:
                        'linear-gradient(to top, rgba(0,0,0,0.85) 30%, rgba(0,0,0,0.3) 100%)',
                    }}
                  />
                </div>
              )}

              {/* Content */}
              <div className="relative z-10 p-8 h-full flex flex-col justify-end">
                {feature.tag && (
                  <span
                    className="inline-block mb-4 self-start px-3 py-1 rounded-full text-xs font-semibold"
                    style={{
                      background:
                        feature.dark || feature.image
                          ? 'rgba(232,25,44,0.2)'
                          : 'rgba(232,25,44,0.08)',
                      color: '#E8192C',
                      border: '1px solid rgba(232,25,44,0.3)',
                      letterSpacing: '0.04em',
                    }}
                  >
                    {feature.tag}
                  </span>
                )}
                <h3
                  className="font-display font-bold mb-2"
                  style={{
                    fontSize: feature.span ? '1.5rem' : '1.125rem',
                    color: feature.dark || feature.image ? '#FFFFFF' : '#1D1D1F',
                    letterSpacing: '-0.02em',
                    lineHeight: 1.2,
                  }}
                >
                  {feature.title}
                </h3>
                <p
                  style={{
                    fontSize: '0.9375rem',
                    color: feature.dark || feature.image ? 'rgba(255,255,255,0.65)' : '#6E6E73',
                    lineHeight: 1.6,
                  }}
                >
                  {feature.desc}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

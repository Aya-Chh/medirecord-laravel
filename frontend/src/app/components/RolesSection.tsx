'use client';

import React, { useEffect, useRef } from 'react';
import AppImage from '@/components/ui/AppImage';

const roles = [
  {
    title: 'Médecins',
    subtitle: 'Praticiens & Spécialistes',
    desc: "Accédez instantanément à l'historique complet de vos patients. Rédigez des ordonnances digitales, partagez des résultats et suivez les évolutions cliniques.",
    benefits: [
      'Historique patient complet',
      'Ordonnances électroniques',
      'Partage sécurisé confrères',
      'Alertes et rappels',
    ],
    image:
      '/assets/images/comment_faire_quand_on_a_pas_de_medecin_traitant_harmonie_sante.jpg-1778499551997.webp',
    imageAlt: 'Médecin en consultation avec un patient, professionnel de santé en blouse blanche',
    cta: 'Rejoindre en tant que médecin',
  },
  {
    title: 'Patients',
    subtitle: 'Tous les profils',
    desc: 'Votre dossier médical complet dans votre poche. Consultez vos résultats, gérez vos rendez-vous et partagez votre dossier avec vos médecins en toute sécurité.',
    benefits: [
      'Dossier toujours accessible',
      "Code QR d'urgence",
      'Rappels médicaments',
      'Historique complet',
    ],
    image: '/assets/images/BXT-980_Renal_Photography_22JULY22_8093_1600x900-1778499738250.jpg',
    imageAlt: 'Patient en soins médicaux, photographie médicale professionnelle haute résolution',
    cta: 'Créer mon dossier',
  },
  {
    title: 'Cliniques',
    subtitle: 'Établissements & Hôpitaux',
    desc: 'Digitalisez vos processus administratifs et médicaux. Gérez vos équipes, vos patients et vos flux documentaires depuis une plateforme centralisée.',
    benefits: [
      'Gestion multi-praticiens',
      'Workflows personnalisés',
      'Rapports & analytics',
      'Intégration SIH',
    ],
    image: '/assets/images/images__6_-1778499807176.jpg',
    imageAlt: "Couloir moderne d'une clinique ou hôpital, établissement de santé contemporain",
    cta: 'Solution établissement',
  },
];

export default function RolesSection() {
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
      { threshold: 0.08 }
    );
    cards.forEach((c) => observer.observe(c));
    return () => observer.disconnect();
  }, []);

  return (
    <section id="roles" ref={sectionRef} className="py-28" style={{ background: '#F5F5F7' }}>
      <div className="max-w-[1200px] mx-auto px-6">
        {/* Header */}
        <div className="text-center mb-16">
          <p className="section-eyebrow mb-3">Pour Tous</p>
          <h2
            className="font-display font-black"
            style={{
              fontSize: 'clamp(2.5rem, 5vw, 4.5rem)',
              color: '#1D1D1F',
              letterSpacing: '-0.04em',
              lineHeight: 1.05,
            }}
          >
            Conçu pour chaque rôle.
          </h2>
        </div>

        {/* Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {roles.map((role, i) => (
            <div
              key={role.title}
              data-reveal
              className="relative overflow-hidden rounded-3xl group flex flex-col"
              style={{
                opacity: 0,
                transform: 'translateY(28px)',
                transition: `opacity 0.8s cubic-bezier(0.23,1,0.32,1) ${i * 100}ms, transform 0.8s cubic-bezier(0.23,1,0.32,1) ${i * 100}ms`,
                background: '#1D1D1F',
                boxShadow: '0 4px 30px rgba(0,0,0,0.12)',
              }}
            >
              {/* HD Image */}
              <div className="relative overflow-hidden" style={{ height: '240px' }}>
                <AppImage
                  src={role.image}
                  alt={role.imageAlt}
                  fill
                  className="object-cover transition-transform duration-700 group-hover:scale-105"
                  sizes="(max-width: 768px) 100vw, 33vw"
                />
                <div
                  className="absolute inset-0"
                  style={{
                    background:
                      'linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(29,29,31,0.7) 100%)',
                  }}
                />
                {/* Title overlay on image */}
                <div className="absolute bottom-5 left-6">
                  <div
                    className="font-display font-black text-white"
                    style={{ fontSize: '1.75rem', letterSpacing: '-0.03em', lineHeight: 1 }}
                  >
                    {role.title}
                  </div>
                  <div
                    style={{
                      fontSize: '0.8125rem',
                      color: 'rgba(255,255,255,0.6)',
                      marginTop: '4px',
                    }}
                  >
                    {role.subtitle}
                  </div>
                </div>
              </div>

              {/* Content */}
              <div className="p-7 flex flex-col gap-5 flex-1">
                <p
                  style={{
                    fontSize: '0.9375rem',
                    color: 'rgba(255,255,255,0.65)',
                    lineHeight: 1.65,
                  }}
                >
                  {role.desc}
                </p>

                <ul className="flex flex-col gap-2.5">
                  {role.benefits.map((benefit) => (
                    <li
                      key={benefit}
                      className="flex items-center gap-3"
                      style={{ fontSize: '0.875rem', color: 'rgba(255,255,255,0.8)' }}
                    >
                      <span
                        className="w-1.5 h-1.5 rounded-full shrink-0"
                        style={{ background: '#E8192C' }}
                      />
                      {benefit}
                    </li>
                  ))}
                </ul>

                <a
                  href="#inscription"
                  className="mt-auto text-center py-3 px-5 rounded-full font-medium transition-all duration-300"
                  style={{
                    display: 'block',
                    fontSize: '0.875rem',
                    color: '#E8192C',
                    border: '1px solid rgba(232,25,44,0.4)',
                    background: 'rgba(232,25,44,0.08)',
                  }}
                  onMouseEnter={(e) => {
                    e.currentTarget.style.background = '#E8192C';
                    e.currentTarget.style.color = '#FFFFFF';
                  }}
                  onMouseLeave={(e) => {
                    e.currentTarget.style.background = 'rgba(232,25,44,0.08)';
                    e.currentTarget.style.color = '#E8192C';
                  }}
                >
                  {role.cta}
                </a>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

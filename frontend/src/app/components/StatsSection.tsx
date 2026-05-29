'use client';

import React, { useEffect, useRef, useState } from 'react';

interface StatItem {
  value: number;
  suffix: string;
  label: string;
  sublabel: string;
}

const stats: StatItem[] = [
  { value: 50000, suffix: '+', label: 'Patients actifs', sublabel: 'dossiers sur la plateforme' },
  { value: 2000, suffix: '+', label: 'Médecins partenaires', sublabel: 'à travers le Maroc' },
  { value: 98, suffix: '%', label: 'Satisfaction', sublabel: 'taux utilisateurs' },
  { value: 15, suffix: ' min', label: 'Pour démarrer', sublabel: 'intégration complète' },
];

function useCounter(target: number, duration: number, active: boolean) {
  const [count, setCount] = useState(0);
  useEffect(() => {
    if (!active) return;
    let start = 0;
    const increment = target / (duration / 16);
    const timer = setInterval(() => {
      start += increment;
      if (start >= target) {
        setCount(target);
        clearInterval(timer);
      } else {
        setCount(Math.floor(start));
      }
    }, 16);
    return () => clearInterval(timer);
  }, [active, target, duration]);
  return count;
}

function StatItem({ stat, active, delay }: { stat: StatItem; active: boolean; delay: number }) {
  const count = useCounter(stat.value, 1800 + delay, active);
  return (
    <div
      className="flex flex-col items-center text-center py-10 px-8"
      style={{
        borderRight: '1px solid rgba(0,0,0,0.08)',
      }}
    >
      <div
        className="font-display font-black mb-2"
        style={{
          fontSize: 'clamp(2.5rem, 5vw, 4.5rem)',
          color: '#1D1D1F',
          letterSpacing: '-0.04em',
          lineHeight: 1,
        }}
      >
        {count.toLocaleString('fr-FR')}
        <span style={{ color: '#E8192C' }}>{stat.suffix}</span>
      </div>
      <div
        className="font-medium mb-1"
        style={{ fontSize: '1rem', color: '#1D1D1F', letterSpacing: '-0.01em' }}
      >
        {stat.label}
      </div>
      <div style={{ fontSize: '0.8125rem', color: '#6E6E73' }}>{stat.sublabel}</div>
    </div>
  );
}

export default function StatsSection() {
  const sectionRef = useRef<HTMLElement>(null);
  const [active, setActive] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) setActive(true);
      },
      { threshold: 0.3 }
    );
    if (sectionRef.current) observer.observe(sectionRef.current);
    return () => observer.disconnect();
  }, []);

  return (
    <section ref={sectionRef} className="relative" style={{ background: '#FFFFFF' }}>
      <div
        className="max-w-[1200px] mx-auto"
        style={{ borderBottom: '1px solid rgba(0,0,0,0.08)' }}
      >
        <div className="grid grid-cols-2 lg:grid-cols-4">
          {stats.map((stat, i) => (
            <StatItem key={stat.label} stat={stat} active={active} delay={i * 150} />
          ))}
        </div>
      </div>
    </section>
  );
}

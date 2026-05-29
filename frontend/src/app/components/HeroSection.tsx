'use client';

import React, { useEffect, useRef, useState } from 'react';
import AppImage from '@/components/ui/AppImage';

export default function HeroSection() {
  const [loaded, setLoaded] = useState(false);
  const parallaxRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const timer = setTimeout(() => setLoaded(true), 100);
    return () => clearTimeout(timer);
  }, []);

  useEffect(() => {
    const onScroll = () => {
      if (!parallaxRef?.current) return;
      const y = window.scrollY;
      parallaxRef.current.style.transform = `scale(1.05) translateY(${y * 0.25}px)`;
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <section
      className="relative min-h-screen flex flex-col items-center justify-center overflow-hidden"
      style={{ background: '#000' }}
    >
      {/* Full-bleed HD background image */}
      <div
        ref={parallaxRef}
        className="absolute inset-0 z-0"
        style={{
          transform: 'scale(1.05)',
          willChange: 'transform',
        }}
      >
        <AppImage
          src="/assets/images/im66-1778497644150.jpg"
          alt="Scanner IRM cérébral haute résolution, coupes axiales lumineuses sur fond sombre"
          fill
          className="object-cover"
          priority
          sizes="100vw"
        />
        {/* Cinematic gradient overlay */}
        <div
          className="absolute inset-0"
          style={{
            background:
              'linear-gradient(to bottom, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.3) 40%, rgba(0,0,0,0.65) 100%)',
          }}
        />
      </div>

      {/* Content */}
      <div
        className="relative z-10 text-center px-6 max-w-[900px] mx-auto"
        style={{ paddingTop: '80px' }}
      >
        {/* Eyebrow */}
        <div
          className="inline-flex items-center gap-2 mb-8"
          style={{
            opacity: loaded ? 1 : 0,
            transform: loaded ? 'translateY(0)' : 'translateY(20px)',
            transition:
              'opacity 0.9s cubic-bezier(0.23,1,0.32,1) 0.1s, transform 0.9s cubic-bezier(0.23,1,0.32,1) 0.1s',
          }}
        >
          <span
            className="px-4 py-1.5 rounded-full text-xs font-medium tracking-widest uppercase"
            style={{
              background: 'rgba(255,255,255,0.12)',
              backdropFilter: 'blur(10px)',
              border: '1px solid rgba(255,255,255,0.2)',
              color: 'rgba(255,255,255,0.9)',
              letterSpacing: '0.12em',
            }}
          >
            Dossier Médical Digital · Maroc
          </span>
        </div>

        {/* Main headline */}
        <h1
          className="font-display font-black text-white mb-6"
          style={{
            fontSize: 'clamp(3rem, 8vw, 7.5rem)',
            lineHeight: '1.0',
            letterSpacing: '-0.04em',
            opacity: loaded ? 1 : 0,
            transform: loaded ? 'translateY(0)' : 'translateY(30px)',
            transition:
              'opacity 0.9s cubic-bezier(0.23,1,0.32,1) 0.25s, transform 0.9s cubic-bezier(0.23,1,0.32,1) 0.25s',
          }}
        >
          Votre santé,
          <br />
          <span style={{ color: '#FF4D5E' }}>partout.</span>
        </h1>

        {/* Subheadline */}
        <p
          className="text-white/70 max-w-xl mx-auto mb-10 leading-relaxed"
          style={{
            fontSize: 'clamp(1rem, 1.5vw, 1.25rem)',
            fontWeight: 300,
            opacity: loaded ? 1 : 0,
            transform: loaded ? 'translateY(0)' : 'translateY(20px)',
            transition:
              'opacity 0.9s cubic-bezier(0.23,1,0.32,1) 0.4s, transform 0.9s cubic-bezier(0.23,1,0.32,1) 0.4s',
          }}
        >
          MediRecord centralise l&apos;intégralité de votre parcours médical — consultations,
          ordonnances, imagerie — dans un espace sécurisé accessible à vous et vos médecins, à tout
          instant.
        </p>
      </div>

      {/* Scroll indicator */}
      <div
        className="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2"
        style={{
          opacity: loaded ? 0.6 : 0,
          transition: 'opacity 1s ease 1.2s',
        }}
      >
        <div
          className="w-px h-12"
          style={{
            background: 'linear-gradient(to bottom, rgba(255,255,255,0.7), transparent)',
            animation: 'float-slow 2s ease-in-out infinite',
          }}
        />
      </div>
    </section>
  );
}

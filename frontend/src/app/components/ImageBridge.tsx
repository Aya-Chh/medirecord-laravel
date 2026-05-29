'use client';

import React, { useEffect, useRef } from 'react';
import AppImage from '@/components/ui/AppImage';

interface ImageBridgeProps {
  imageSrc: string;
  imageAlt: string;
  label: string;
  index: number;
}

export default function ImageBridge({ imageSrc, imageAlt, label }: ImageBridgeProps) {
  const containerRef = useRef<HTMLDivElement>(null);
  const imageRef = useRef<HTMLDivElement>(null);
  const textRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = containerRef.current;
    const image = imageRef.current;
    const text = textRef.current;
    if (!container || !image) return;

    let ticking = false;

    const onScroll = () => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => {
        const rect = container.getBoundingClientRect();
        const vh = window.innerHeight;
        const progress = 1 - (rect.top + rect.height) / (vh + rect.height);
        const clamped = Math.max(0, Math.min(1, progress));

        // Parallax on the inner image
        const translateY = (clamped - 0.5) * 60;
        image.style.transform = `translateY(${translateY}px) scale(1.08)`;

        // Fade in/out
        const opacity = clamped < 0.08 ? clamped / 0.08 : clamped > 0.92 ? (1 - clamped) / 0.08 : 1;
        container.style.opacity = String(Math.max(0, opacity));

        if (text) {
          const textOpacity = clamped > 0.25 && clamped < 0.75 ? 1 : 0;
          const textY = (0.5 - clamped) * 20;
          text.style.opacity = String(textOpacity);
          text.style.transform = `translate(-50%, calc(-50% + ${textY}px))`;
        }

        ticking = false;
      });
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <div ref={containerRef} className="relative w-full overflow-hidden" style={{ height: '100vh' }}>
      {/* Full-bleed image with parallax */}
      <div
        ref={imageRef}
        className="absolute inset-0"
        style={{
          willChange: 'transform',
          transform: 'scale(1.08)',
        }}
      >
        <AppImage src={imageSrc} alt={imageAlt} fill className="object-cover" sizes="100vw" />
        {/* Subtle dark overlay for legibility */}
        <div
          className="absolute inset-0"
          style={{
            background:
              'linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.3) 100%)',
          }}
        />
      </div>

      {/* Centered label */}
      <div
        ref={textRef}
        className="absolute left-1/2 top-1/2 z-10 pointer-events-none"
        style={{
          transform: 'translate(-50%, -50%)',
          transition: 'opacity 0.5s ease, transform 0.5s ease',
          opacity: 0,
          willChange: 'transform, opacity',
        }}
      >
        <div
          className="flex items-center gap-3 px-6 py-3 rounded-full"
          style={{
            background: 'rgba(255,255,255,0.12)',
            backdropFilter: 'blur(16px)',
            border: '1px solid rgba(255,255,255,0.25)',
          }}
        >
          <span className="w-1.5 h-1.5 rounded-full" style={{ background: '#E8192C' }} />
          <span
            className="text-white font-medium tracking-widest uppercase"
            style={{ fontSize: '0.6875rem', letterSpacing: '0.18em' }}
          >
            {label}
          </span>
        </div>
      </div>
    </div>
  );
}

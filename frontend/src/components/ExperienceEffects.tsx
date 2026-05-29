'use client';

import { useEffect } from 'react';
import Lenis from 'lenis';
import { animate, stagger } from 'animejs';

const LENIS_SMOOTH_DURATION = 1.25;

export default function ExperienceEffects() {
  useEffect(() => {
    const lenis = new Lenis({
      duration: LENIS_SMOOTH_DURATION,
      smoothWheel: true,
      wheelMultiplier: 0.9,
      touchMultiplier: 1.15,
    });

    let frame = 0;
    function raf(time: number) {
      lenis.raf(time);
      frame = requestAnimationFrame(raf);
    }
    frame = requestAnimationFrame(raf);

    animate('.hero-copy > *', {
      opacity: [0, 1],
      y: [24, 0],
      duration: 820,
      delay: stagger(95),
      ease: 'out(4)',
    });

    const reveal = () => {
      const items = Array.from(document.querySelectorAll<HTMLElement>('[data-reveal]')).filter(
        (item) => !item.dataset.revealed
      );

      const visible = items.filter((item) => {
        const rect = item.getBoundingClientRect();
        return rect.top < window.innerHeight * 0.88;
      });

      if (visible.length > 0) {
        visible.forEach((item) => {
          item.dataset.revealed = 'true';
        });
        animate(visible, {
          opacity: [0, 1],
          y: [28, 0],
          duration: 760,
          delay: stagger(70),
          ease: 'out(3)',
        });
      }
    };

    reveal();
    window.addEventListener('scroll', reveal, { passive: true });
    window.addEventListener('resize', reveal);

    return () => {
      cancelAnimationFrame(frame);
      window.removeEventListener('scroll', reveal);
      window.removeEventListener('resize', reveal);
      lenis.destroy();
    };
  }, []);

  return null;
}

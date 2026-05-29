import React from 'react';
import type { Metadata, Viewport } from 'next';
import { Syne, DM_Sans } from 'next/font/google';
import '../styles/tailwind.css';
import ExperienceEffects from '@/components/ExperienceEffects';

const syne = Syne({
  subsets: ['latin'],
  weight: ['400', '500', '600', '700', '800'],
  variable: '--font-syne',
  display: 'swap',
});

const dmSans = DM_Sans({
  subsets: ['latin'],
  weight: ['300', '400', '500', '600'],
  variable: '--font-dm-sans',
  display: 'swap',
});

export const viewport: Viewport = {
  width: 'device-width',
  initialScale: 1,
};

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000'),
  title: 'MediRecord — Dossiers Médicaux Numériques au Maroc',
  description:
    'MediRecord centralise vos dossiers médicaux pour médecins et patients au Maroc. Accès sécurisé, partage instantané, conformité totale.',
  icons: {
    icon: [{ url: '/favicon.ico', type: 'image/x-icon' }],
  },
  openGraph: {
    title: 'MediRecord — Santé Numérique',
    description: 'La plateforme de référence pour les dossiers médicaux au Maroc.',
    images: [{ url: '/assets/images/app_logo.png', width: 1200, height: 630 }],
  },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="fr" className={`${syne.variable} ${dmSans.variable}`}>
      <body className={dmSans.className}>
        <ExperienceEffects />
        {children}
      </body>
    </html>
  );
}

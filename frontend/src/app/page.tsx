import React from 'react';
import Link from 'next/link';
import {
  ArrowRight,
  Bot,
  FileText,
  History,
  LockKeyhole,
  ScanText,
  ShieldCheck,
  Stethoscope,
  UserRound,
} from 'lucide-react';
import MediBot from '@/components/MediBot';

const services = [
  {
    icon: UserRound,
    title: 'Espace patient',
    text: 'Le patient accède à son dossier avec son CIN et sa date de naissance. Il consulte son médecin récent et ses traitements validés.',
  },
  {
    icon: Stethoscope,
    title: 'Espace médecin',
    text: 'Le médecin retrouve un patient, ajoute une ordonnance et valide les informations avant leur enregistrement.',
  },
  {
    icon: ScanText,
    title: 'Extraction assistée',
    text: "Le contenu d'une ordonnance peut être saisi ou importé, puis transformé en texte relisible et modifiable.",
  },
  {
    icon: History,
    title: 'Historique confidentiel',
    text: 'Les ordonnances validées sont conservées sous forme de texte afin de faciliter le suivi sans exposer les scans.',
  },
];

export default function HomePage() {
  return (
    <main
      className="app-shell photo-backdrop min-h-screen text-white"
      style={{ '--page-photo': "url('/assets/images/im66-1778497644150.jpg')" } as React.CSSProperties}
    >
      <section className="min-h-screen px-5 py-6 sm:px-8 lg:px-10">
        <div className="mx-auto flex min-h-[calc(100vh-3rem)] max-w-7xl flex-col">
          <header className="flex items-center justify-between gap-4">
            <Link href="/" className="flex items-center gap-3">
              <span className="grid h-10 w-10 place-items-center rounded-lg bg-[#E8192C] text-lg font-black text-white shadow-lg shadow-red-500/25">
                +
              </span>
              <span className="font-display text-xl font-black">MediRecord</span>
            </Link>
            <nav className="flex items-center gap-2 text-sm font-bold">
              <Link href="/patient" className="rounded-lg bg-white px-4 py-2 text-black">
                Patient
              </Link>
              <Link href="/doctor" className="rounded-lg bg-[#E8192C] px-4 py-2 text-white">
                Médecin
              </Link>
            </nav>
          </header>

          <div className="grid flex-1 items-center gap-10 py-14 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
              <div className="mb-6 inline-flex items-center gap-2 rounded-lg border border-red-500/30 bg-black/45 px-3 py-2 text-sm font-bold text-red-100 backdrop-blur">
                <ShieldCheck className="h-4 w-4" />
                Dossier médical numérique et confidentiel
              </div>
              <h1 className="font-display text-5xl font-black leading-[0.98] text-white sm:text-6xl lg:text-7xl">
                Un suivi médical plus clair pour le patient et le médecin.
              </h1>
              <p className="mt-6 max-w-2xl text-lg leading-8 text-white/72">
                MediRecord facilite la consultation des traitements, la validation des ordonnances
                et l'accès à l'historique patient sous une forme simple, lisible et sécurisée.
              </p>
              <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                <Link href="/patient" className="btn-primary gap-2">
                  Accéder à l'espace patient <ArrowRight className="h-4 w-4" />
                </Link>
                <Link href="/doctor" className="btn-outline border-white/35 text-white hover:bg-white hover:text-black">
                  Accéder à l'espace médecin
                </Link>
              </div>
            </div>

            <div className="grid gap-4">
              <article className="dark-panel p-6">
                <LockKeyhole className="mb-5 h-8 w-8 text-[#E8192C]" />
                <h2 className="font-display text-3xl font-black">Confidentialité avant tout.</h2>
                <p className="mt-4 leading-8 text-white/68">
                  Les informations essentielles sont présentées en texte. Les médecins consultent
                  l'historique utile sans afficher les documents originaux à chaque visite.
                </p>
              </article>
              <article className="glass-panel p-6">
                <Bot className="mb-5 h-8 w-8 text-[#E8192C]" />
                <h2 className="font-display text-3xl font-black">MediBot accompagne l'utilisateur.</h2>
                <p className="mt-4 leading-8 text-white/68">
                  L assistant explique comment utiliser la plateforme, comment consulter un dossier
                  et comment ajouter une ordonnance. Il ne remplace pas un avis médical.
                </p>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section className="px-5 pb-20 sm:px-8 lg:px-10">
        <div className="mx-auto max-w-7xl">
          <div data-reveal className="mb-8 max-w-3xl">
            <p className="text-sm font-black uppercase tracking-[0.18em] text-red-300">Fonctionnement</p>
            <h2 className="mt-3 font-display text-4xl font-black leading-tight sm:text-5xl">
              Une plateforme organisée autour des usages réels.
            </h2>
          </div>
          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            {services.map((service) => {
              const Icon = service.icon;
              return (
                <article key={service.title} data-reveal className="premium-card">
                  <div className="mb-5 grid h-11 w-11 place-items-center rounded-lg bg-[#E8192C] text-white">
                    <Icon className="h-5 w-5" />
                  </div>
                  <h3 className="font-display text-2xl font-black">{service.title}</h3>
                  <p className="mt-3 leading-7 text-white/62">{service.text}</p>
                </article>
              );
            })}
          </div>
        </div>
      </section>

      <section className="px-5 pb-24 sm:px-8 lg:px-10">
        <div className="mx-auto grid max-w-7xl gap-5 lg:grid-cols-[0.82fr_1.18fr]">
          <div data-reveal className="dark-panel p-6 text-white sm:p-8">
            <FileText className="mb-5 h-8 w-8 text-[#E8192C]" />
            <h2 className="font-display text-4xl font-black">Des ordonnances plus faciles à suivre.</h2>
            <p className="mt-4 leading-8 text-white/70">
              Après validation par le médecin, chaque ordonnance rejoint l'historique du patient.
              Le contenu reste clair, structuré et consultable lors des prochaines visites.
            </p>
          </div>
          <div data-reveal className="premium-photo-panel">
            <img
              src="/assets/images/comment_faire_quand_on_a_pas_de_medecin_traitant_harmonie_sante.jpg-1778499551997.webp"
              alt="Médecin consultant un dossier médical numérique"
            />
            <div>
              <Stethoscope className="h-7 w-7 text-[#E8192C]" />
              <h3>Une interface pensée pour travailler vite, sans perdre la précision médicale.</h3>
            </div>
          </div>
        </div>
      </section>

      <MediBot defaultOpen={false} />
    </main>
  );
}

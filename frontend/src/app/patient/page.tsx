'use client';

import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { CalendarDays, FileText, Home, LogOut, ShieldCheck, UserRound } from 'lucide-react';
import MediBot from '@/components/MediBot';
import {
  MediPatient,
  MediPrescription,
  mediPatientDashboard,
  mediPatientLogin,
  mediPatientRegister,
  sendEmailJs,
} from '@/lib.ts/api';

export default function PatientPage() {
  const [mode, setMode] = useState<'login' | 'register'>('login');
  const [patient, setPatient] = useState<MediPatient | null>(null);
  const [doctor, setDoctor] = useState<{ name: string; specialty: string; sector: string } | null>(
    null
  );
  const [prescriptions, setPrescriptions] = useState<MediPrescription[]>([]);
  const [form, setForm] = useState({ name: '', email: '', cin: '', birth_date: '' });
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const stored = window.localStorage.getItem('medi_patient');
    if (stored) {
      const parsed = JSON.parse(stored) as MediPatient;
      setPatient(parsed);
      void loadDashboard(parsed.id);
    }
  }, []);

  const loadDashboard = async (patientId: number) => {
    const response = await mediPatientDashboard(patientId);
    setDoctor(response.data.doctor);
    setPrescriptions(response.data.prescriptions);
  };

  const register = async (event?: React.FormEvent) => {
    event?.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      const response = await mediPatientRegister(form);
      const registeredPatient = response.data.patient;
      await sendEmailJs(process.env.NEXT_PUBLIC_EMAILJS_PATIENT_TEMPLATE_ID || '', {
        to_email: registeredPatient.email,
        patient_name: registeredPatient.name || registeredPatient.cin,
        message: 'Bienvenue sur MediRecord. Votre espace patient est maintenant créé.',
      }).catch(() => null);
      setMessage('Inscription réussie. Un email de bienvenue a été demandé via EmailJS.');
      setMode('login');
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Inscription impossible.');
    } finally {
      setLoading(false);
    }
  };

  const login = async (event?: React.FormEvent) => {
    event?.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      const response = await mediPatientLogin({
        cin: form.cin,
        birth_date: form.birth_date,
      });
      setPatient(response.data.patient);
      window.localStorage.setItem('medi_patient', JSON.stringify(response.data.patient));
      window.localStorage.setItem('medi_patient_token', response.data.token);
      await loadDashboard(response.data.patient.id);
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Connexion impossible.');
    } finally {
      setLoading(false);
    }
  };

  const logout = () => {
    window.localStorage.removeItem('medi_patient');
    window.localStorage.removeItem('medi_patient_token');
    setPatient(null);
    setDoctor(null);
    setPrescriptions([]);
  };

  return (
    <main
      className="app-shell photo-backdrop min-h-screen px-5 py-6 text-white sm:px-8 lg:px-10"
      style={{ '--page-photo': "url('/assets/images/im2-1778497653011.jpg')" } as React.CSSProperties}
    >
      <div className="mx-auto max-w-7xl">
        <header className="motion-rise mb-8 flex items-center justify-between gap-4">
          <Link href="/" className="flex items-center gap-3">
            <span className="grid h-10 w-10 place-items-center rounded-lg bg-[#E8192C] font-black text-white">
              +
            </span>
            <span className="font-display text-xl font-black text-white">MediRecord Patient</span>
          </Link>
          <div className="flex flex-wrap items-center justify-end gap-3">
            <Link href="/" className="btn-outline gap-2 border-white/35 text-white hover:bg-white hover:text-black">
              <Home className="h-4 w-4" />
              Retour à l'accueil
            </Link>
            {patient && (
              <button type="button" onClick={logout} className="btn-primary gap-2">
                <LogOut className="h-4 w-4" />
                Déconnexion
              </button>
            )}
          </div>
        </header>

        {!patient ? (
          <section className="grid items-stretch gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            <div className="scene-depth motion-rise-delay relative min-h-[520px] overflow-hidden rounded-lg border border-white/10 bg-black/40 backdrop-blur">
              <div className="absolute inset-0 bg-gradient-to-br from-red-600/20 via-black/20 to-black/70" />
              <div className="absolute bottom-0 left-0 right-0 p-6 text-white sm:p-8">
                <div className="mb-4 inline-flex items-center gap-2 rounded-lg bg-red-600/18 px-3 py-2 text-sm font-bold backdrop-blur">
                  <ShieldCheck className="h-4 w-4 text-red-200" />
                  Accès confidentiel
                </div>
                <h1 className="font-display text-4xl font-black leading-tight sm:text-5xl">
                  Votre dossier, lisible et protégé.
                </h1>
                <p className="mt-4 max-w-xl leading-7 text-white/75">
                  Connectez-vous avec CIN et date de naissance pour consulter le médecin récent
                  et les traitements déjà validés sous forme texte.
                </p>
              </div>
            </div>

            <form onSubmit={mode === 'register' ? register : login} className="glass-panel p-5 sm:p-8">
              <div className="mb-6 flex flex-wrap gap-2">
                <button
                  type="button"
                  onClick={() => setMode('login')}
                  className={`soft-tab ${mode === 'login' ? 'bg-[#E8192C] text-white' : 'bg-white/10 text-white/70'}`}
                >
                  Connexion
                </button>
                <button
                  type="button"
                  onClick={() => setMode('register')}
                  className={`soft-tab ${mode === 'register' ? 'bg-[#E8192C] text-white' : 'bg-white/10 text-white/70'}`}
                >
                  Première inscription
                </button>
              </div>

              <h2 className="font-display text-3xl font-black">
                {mode === 'register' ? 'Créer mon espace patient' : 'Accéder à mon dossier'}
              </h2>
              <p className="mt-2 text-white/60">
                {mode === 'register'
                  ? 'Renseignez vos informations de première inscription.'
                  : 'Utilisez votre CIN et votre date de naissance.'}
              </p>

              <div className="mt-7 grid gap-4">
                {mode === 'register' && (
                  <>
                    <input
                      value={form.name}
                      onChange={(event) => setForm({ ...form, name: event.target.value })}
                      placeholder="Nom complet"
                      className="field-control"
                    />
                    <input
                      type="email"
                      value={form.email}
                      onChange={(event) => setForm({ ...form, email: event.target.value })}
                      placeholder="Email"
                      required
                      className="field-control"
                    />
                  </>
                )}
                <input
                  value={form.cin}
                  onChange={(event) => setForm({ ...form, cin: event.target.value })}
                  placeholder="CIN"
                  required
                  className="field-control uppercase"
                />
                <input
                  type="date"
                  value={form.birth_date}
                  onChange={(event) => setForm({ ...form, birth_date: event.target.value })}
                  onInput={(event) =>
                    setForm({ ...form, birth_date: (event.target as HTMLInputElement).value })
                  }
                  required
                  className="field-control"
                />
                {message && (
                  <p className="rounded-lg bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-200">
                    {message}
                  </p>
                )}
                <button
                  type="button"
                  onClick={() => {
                    if (mode === 'register') {
                      void register();
                    } else {
                      void login();
                    }
                  }}
                  disabled={loading}
                  className="btn-primary w-full"
                >
                  {loading ? 'Veuillez patienter...' : mode === 'register' ? "S'inscrire" : 'Se connecter'}
                </button>
              </div>
            </form>
          </section>
        ) : (
          <section className="space-y-6">
            <div className="motion-rise grid gap-5 lg:grid-cols-[1.2fr_0.8fr]">
              <div className="dark-panel p-6 text-white sm:p-8">
                <p className="mb-3 text-sm font-bold uppercase tracking-[0.16em] text-red-300">
                  Mon dossier
                </p>
                <h1 className="font-display text-4xl font-black">Bonjour {patient.name || patient.masked_cin}</h1>
                <p className="mt-3 text-white/65">CIN: {patient.masked_cin}</p>
              </div>
              <div className="glass-panel p-6">
                <div className="mb-4 grid h-11 w-11 place-items-center rounded-lg bg-[#E8192C] text-white">
                  <UserRound className="h-5 w-5" />
                </div>
                <p className="text-sm font-bold uppercase tracking-[0.16em] text-white/45">Médecin récent</p>
                {doctor ? (
                  <div className="mt-3">
                    <h2 className="font-display text-2xl font-black">{doctor.name}</h2>
                    <p className="mt-1 text-white/65">{doctor.specialty}</p>
                  <p className="text-sm text-white/45">Secteur {doctor.sector}</p>
                  </div>
                ) : (
                  <p className="mt-3 text-white/60">Aucun traitement valide pour le moment.</p>
                )}
              </div>
            </div>

            <div className="glass-panel p-5 sm:p-8">
              <div className="mb-6 flex items-center gap-3">
                <div className="grid h-11 w-11 place-items-center rounded-lg bg-[#E8192C] text-white">
                  <FileText className="h-5 w-5" />
                </div>
                <div>
                  <h2 className="font-display text-2xl font-black">Traitements et ordonnances</h2>
                  <p className="text-sm text-white/45">Historique validé par les médecins</p>
                </div>
              </div>
              <div className="grid gap-4">
                {prescriptions.length === 0 ? (
                  <div className="rounded-lg border border-dashed border-white/18 bg-white/5 p-8 text-center text-white/60">
                    Aucune ordonnance enregistrée.
                  </div>
                ) : (
                  prescriptions.map((item) => (
                    <article key={item.id} className="tilt-card border border-white/12 bg-black/35 p-5">
                      <div className="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <h3 className="font-display text-xl font-black">{item.title}</h3>
                        <span className="inline-flex items-center gap-2 text-xs font-bold text-white/45">
                          <CalendarDays className="h-4 w-4" />
                          {item.validated_at}
                        </span>
                      </div>
                      <p className="whitespace-pre-line text-sm leading-7 text-white/70">{item.text}</p>
                      <p className="mt-4 text-xs font-bold text-red-300">
                        Validé par {item.doctor} - {item.specialty}
                      </p>
                    </article>
                  ))
                )}
              </div>
            </div>
          </section>
        )}
      </div>
      <MediBot defaultOpen={false} />
    </main>
  );
}

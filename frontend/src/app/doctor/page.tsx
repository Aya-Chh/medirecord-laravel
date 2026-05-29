'use client';

import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { BrainCircuit, FileSearch, History, Home, LogOut, ScanLine, Stethoscope } from 'lucide-react';
import MediBot from '@/components/MediBot';
import {
  MediDoctor,
  MediPatient,
  MediPrescription,
  mediDoctorActivateCode,
  mediDoctorLogin,
  mediDoctorRegister,
  mediExtractPrescription,
  mediFindPatient,
  mediPatientHistory,
  mediStorePrescription,
  sendEmailJs,
} from '@/lib.ts/api';

export default function DoctorPage() {
  const [mode, setMode] = useState<'login' | 'activate' | 'register'>('login');
  const [doctor, setDoctor] = useState<MediDoctor | null>(null);
  const [selectedPatient, setSelectedPatient] = useState<MediPatient | null>(null);
  const [history, setHistory] = useState<MediPrescription[]>([]);
  const [extractedText, setExtractedText] = useState('');
  const [sourceFileName, setSourceFileName] = useState<string | null>(null);
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);
  const [loginCode, setLoginCode] = useState('');
  const [activationForm, setActivationForm] = useState({ email_code: '', new_code: '' });
  const [patientLookup, setPatientLookup] = useState({ cin: '', birth_date: '' });
  const [typedText, setTypedText] = useState('');
  const [file, setFile] = useState<File | null>(null);
  const [registerForm, setRegisterForm] = useState({
    email: '',
    first_name: '',
    last_name: '',
    profession: '',
    specialty: '',
    sector: 'prive',
    professional_code: '',
  });

  useEffect(() => {
    const stored = window.localStorage.getItem('medi_doctor');
    if (stored) setDoctor(JSON.parse(stored));
  }, []);

  const register = async (event?: React.FormEvent) => {
    event?.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      const response = await mediDoctorRegister(registerForm);
      await sendEmailJs(process.env.NEXT_PUBLIC_EMAILJS_DOCTOR_TEMPLATE_ID || '', {
        to_email: response.data.doctor.email,
        doctor_name: response.data.doctor.name,
        daily_code: response.data.daily_code,
        message: `Votre code d'activation MediRecord est ${response.data.daily_code}`,
      }).catch(() => null);
      setMessage(
        `Inscription réussie. Code reçu par email: ${response.data.daily_code}. Utilisez-le dans l'onglet "Activer le code" pour créer votre code personnel.`
      );
      setMode('activate');
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Inscription impossible.');
    } finally {
      setLoading(false);
    }
  };

  const activateCode = async (event?: React.FormEvent) => {
    event?.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      const response = await mediDoctorActivateCode(activationForm);
      setMessage(response.message);
      setActivationForm({ email_code: '', new_code: '' });
      setMode('login');
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Activation impossible.');
    } finally {
      setLoading(false);
    }
  };

  const login = async (event?: React.FormEvent) => {
    event?.preventDefault();
    setLoading(true);
    setMessage('');

    try {
      const response = await mediDoctorLogin(loginCode);
      setDoctor(response.data.doctor);
      window.localStorage.setItem('medi_doctor', JSON.stringify(response.data.doctor));
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Connexion impossible.');
    } finally {
      setLoading(false);
    }
  };

  const findPatient = async (event?: React.FormEvent) => {
    event?.preventDefault();
    if (!doctor) return;
    setLoading(true);
    setMessage('');
    setHistory([]);
    setExtractedText('');

    try {
      const response = await mediFindPatient({
        doctor_id: doctor.id,
        cin: patientLookup.cin,
        birth_date: patientLookup.birth_date,
      });
      setSelectedPatient(response.data.patient);
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Patient introuvable.');
    } finally {
      setLoading(false);
    }
  };

  const extract = async () => {
    if (!doctor || !selectedPatient) return;
    if (!typedText.trim() && !file) {
      setMessage('Ajoutez un fichier ordonnance ou saisissez un texte avant extraction.');
      return;
    }
    setLoading(true);
    setMessage('');

    try {
      const response = await mediExtractPrescription({
        doctor_id: doctor.id,
        patient_id: selectedPatient.id,
        typed_text: typedText,
        file,
      });
      setExtractedText(response.data.extracted_text);
      setSourceFileName(response.data.source_file_name || null);
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Extraction impossible.');
    } finally {
      setLoading(false);
    }
  };

  const validatePrescription = async () => {
    if (!doctor || !selectedPatient || !extractedText.trim()) return;
    setLoading(true);
    setMessage('');

    try {
      await mediStorePrescription({
        doctor_id: doctor.id,
        patient_id: selectedPatient.id,
        title: 'Ordonnance validée',
        raw_text: typedText,
        ai_text: extractedText,
        source_file_name: sourceFileName,
        status: 'validated',
      });
      setMessage("Ordonnance validée et ajoutée à l'historique patient.");
      setTypedText('');
      setFile(null);
      setExtractedText('');
      await loadHistory();
    } catch (err) {
      setMessage(err instanceof Error ? err.message : 'Validation impossible.');
    } finally {
      setLoading(false);
    }
  };

  const cancelPrescription = () => {
    setTypedText('');
    setFile(null);
    setExtractedText('');
    setSourceFileName(null);
  };

  const loadHistory = async () => {
    if (!doctor || !selectedPatient) return;
    const response = await mediPatientHistory(doctor.id, selectedPatient.id);
    setHistory(response.data.prescriptions);
  };

  const logout = () => {
    window.localStorage.removeItem('medi_doctor');
    setDoctor(null);
    setSelectedPatient(null);
  };

  return (
    <main
      className="app-shell photo-backdrop min-h-screen px-5 py-6 text-white sm:px-8 lg:px-10"
      style={{ '--page-photo': "url('/assets/images/BXT-980_Renal_Photography_22JULY22_8093_1600x900-1778499738250.jpg')" } as React.CSSProperties}
    >
      <div className="mx-auto max-w-7xl">
        <header className="motion-rise mb-8 flex items-center justify-between gap-4">
          <Link href="/" className="flex items-center gap-3">
            <span className="grid h-10 w-10 place-items-center rounded-lg bg-[#E8192C] font-black text-white">
              +
            </span>
            <span className="font-display text-xl font-black text-white">MediRecord Médecin</span>
          </Link>
          <div className="flex flex-wrap items-center justify-end gap-3">
            <Link href="/" className="btn-outline gap-2 border-white/35 text-white hover:bg-white hover:text-black">
              <Home className="h-4 w-4" />
              Retour à l'accueil
            </Link>
            {doctor && (
              <button type="button" onClick={logout} className="btn-primary gap-2">
                <LogOut className="h-4 w-4" />
                Déconnexion
              </button>
            )}
          </div>
        </header>

        {!doctor ? (
          <section className="grid items-stretch gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <div className="scene-depth motion-rise-delay relative min-h-[540px] overflow-hidden rounded-lg border border-white/10 bg-black/40 backdrop-blur">
              <div className="absolute inset-0 bg-gradient-to-br from-red-600/20 via-black/20 to-black/70" />
              <div className="absolute bottom-0 left-0 right-0 p-6 text-white sm:p-8">
                <div className="mb-4 inline-flex items-center gap-2 rounded-lg bg-red-600/18 px-3 py-2 text-sm font-bold backdrop-blur">
                  <BrainCircuit className="h-4 w-4 text-red-200" />
                  IA sous validation médicale
                </div>
                <h1 className="font-display text-4xl font-black leading-tight sm:text-5xl">
                  Un tableau de bord rapide pour vos patients.
                </h1>
                <p className="mt-4 max-w-xl leading-7 text-white/75">
                  Recherchez un patient, ajoutez une ordonnance, corrigez le texte IA et validez
                  seulement quand le contenu est exact.
                </p>
              </div>
            </div>

            <form
              onSubmit={mode === 'register' ? register : mode === 'activate' ? activateCode : login}
              className="glass-panel p-5 sm:p-8"
            >
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
                  onClick={() => setMode('activate')}
                  className={`soft-tab ${mode === 'activate' ? 'bg-[#E8192C] text-white' : 'bg-white/10 text-white/70'}`}
                >
                  Activer le code
                </button>
                <button
                  type="button"
                  onClick={() => setMode('register')}
                  className={`soft-tab ${mode === 'register' ? 'bg-[#E8192C] text-white' : 'bg-white/10 text-white/70'}`}
                >
                  Inscription
                </button>
              </div>

              <h2 className="font-display text-3xl font-black">
                {mode === 'register'
                  ? 'Inscription médecin'
                  : mode === 'activate'
                    ? 'Créer mon code personnel'
                    : 'Connexion par code'}
              </h2>
              <p className="mt-2 text-white/60">
                {mode === 'register'
                  ? 'Créez votre accès professionnel et recevez votre code par email.'
                  : mode === 'activate'
                    ? 'Saisissez le code reçu par email, puis choisissez un nouveau code avec au moins 7 caractères et un caractère spécial.'
                    : 'Entrez uniquement votre code personnel.'}
              </p>

              <div className="mt-7 grid gap-4">
                {mode === 'register' ? (
                  <>
                    <input className="field-control" placeholder="Nom" value={registerForm.last_name} onChange={(e) => setRegisterForm({ ...registerForm, last_name: e.target.value })} required />
                    <input className="field-control" placeholder="Prénom" value={registerForm.first_name} onChange={(e) => setRegisterForm({ ...registerForm, first_name: e.target.value })} required />
                    <input type="email" className="field-control" placeholder="Email" value={registerForm.email} onChange={(e) => setRegisterForm({ ...registerForm, email: e.target.value })} required />
                    <input className="field-control" placeholder="Profession" value={registerForm.profession} onChange={(e) => setRegisterForm({ ...registerForm, profession: e.target.value })} required />
                    <input className="field-control" placeholder="Spécialité" value={registerForm.specialty} onChange={(e) => setRegisterForm({ ...registerForm, specialty: e.target.value })} required />
                    <select className="field-control" value={registerForm.sector} onChange={(e) => setRegisterForm({ ...registerForm, sector: e.target.value })}>
                      <option value="prive">Secteur privé</option>
                      <option value="public">Secteur public</option>
                    </select>
                    <input className="field-control" placeholder="Code confidentiel carte professionnelle" value={registerForm.professional_code} onChange={(e) => setRegisterForm({ ...registerForm, professional_code: e.target.value })} required />
                  </>
                ) : mode === 'activate' ? (
                  <>
                    <input
                      className="field-control"
                      placeholder="Code reçu par email"
                      value={activationForm.email_code}
                      onChange={(e) => setActivationForm({ ...activationForm, email_code: e.target.value })}
                      required
                    />
                    <input
                      className="field-control"
                      placeholder="Nouveau code personnel, ex: Medic@25"
                      value={activationForm.new_code}
                      onChange={(e) => setActivationForm({ ...activationForm, new_code: e.target.value })}
                      minLength={7}
                      required
                    />
                    <p className="text-xs leading-5 text-white/45">
                      Le nouveau code doit contenir au moins 7 caractères et un caractère spécial.
                    </p>
                  </>
                ) : (
                  <input
                    className="field-control"
                    placeholder="Code personnel"
                    value={loginCode}
                    onChange={(e) => setLoginCode(e.target.value)}
                    required
                  />
                )}

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
                    } else if (mode === 'activate') {
                      void activateCode();
                    } else {
                      void login();
                    }
                  }}
                  disabled={loading}
                  className="btn-primary w-full"
                >
                  {loading
                    ? 'Veuillez patienter...'
                    : mode === 'register'
                      ? "S'inscrire"
                      : mode === 'activate'
                        ? 'Valider mon code'
                        : 'Se connecter'}
                </button>
              </div>
            </form>
          </section>
        ) : (
          <section className="space-y-6">
            <div className="motion-rise dark-panel p-6 text-white sm:p-8">
              <p className="mb-3 text-sm font-bold uppercase tracking-[0.16em] text-red-300">
                Tableau de bord médecin
              </p>
              <h1 className="font-display text-4xl font-black">{doctor.name}</h1>
              <p className="mt-2 text-white/65">
                {doctor.profession} - {doctor.specialty} - secteur {doctor.sector}
              </p>
            </div>

            <div className="grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
              <div className="glass-panel p-5 sm:p-7">
                <div className="mb-5 flex items-center gap-3">
                  <div className="grid h-11 w-11 place-items-center rounded-lg bg-[#E8192C] text-white">
                    <Stethoscope className="h-5 w-5" />
                  </div>
                  <div>
                    <h2 className="font-display text-2xl font-black">Nouveau patient</h2>
                    <p className="text-sm text-white/45">Recherche par CIN et date</p>
                  </div>
                </div>
                <form onSubmit={findPatient} className="grid gap-3">
                  <input className="field-control uppercase" placeholder="CIN patient" value={patientLookup.cin} onChange={(e) => setPatientLookup({ ...patientLookup, cin: e.target.value })} required />
                  <input
                    type="date"
                    className="field-control"
                    value={patientLookup.birth_date}
                    onChange={(e) => setPatientLookup({ ...patientLookup, birth_date: e.target.value })}
                    onInput={(e) =>
                      setPatientLookup({
                        ...patientLookup,
                        birth_date: (e.target as HTMLInputElement).value,
                      })
                    }
                    required
                  />
                  <button
                    className="btn-primary"
                    type="button"
                    onClick={() => void findPatient()}
                    disabled={loading}
                  >
                    Rechercher patient
                  </button>
                </form>
                {selectedPatient && (
                  <div className="mt-5 rounded-lg border border-red-500/20 bg-red-500/10 p-4">
                    <p className="font-display text-xl font-black">{selectedPatient.name || selectedPatient.masked_cin}</p>
                    <p className="text-sm text-white/60">{selectedPatient.email}</p>
                  </div>
                )}
              </div>

              <div className="glass-panel p-5 sm:p-7">
                <div className="mb-5 flex items-center gap-3">
                  <div className="grid h-11 w-11 place-items-center rounded-lg bg-[#E8192C] text-white">
                    <ScanLine className="h-5 w-5" />
                  </div>
                  <div>
                    <h2 className="font-display text-2xl font-black">Ordonnance et historique</h2>
                    <p className="text-sm text-white/45">Extraction, correction, validation</p>
                  </div>
                </div>

                {!selectedPatient ? (
                  <div className="rounded-lg border border-dashed border-white/18 bg-white/5 p-8 text-center text-white/60">
                    Recherchez d'abord un patient.
                  </div>
                ) : (
                  <div className="space-y-4">
                    <textarea
                      value={typedText}
                      onChange={(e) => setTypedText(e.target.value)}
                      placeholder="Collez ou saisissez le contenu de l'ordonnance si disponible..."
                      className="field-control min-h-32 resize-y"
                    />
                    <input
                      type="file"
                      accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf"
                      onChange={(e) => setFile(e.target.files?.[0] || null)}
                      className="field-control"
                    />
                    <p className="text-xs leading-5 text-white/45">
                      Formats acceptés: JPG, PNG, WEBP ou PDF. Taille maximale: 10 Mo.
                    </p>
                    <div className="flex flex-wrap gap-3">
                      <button type="button" onClick={extract} disabled={loading} className="btn-primary gap-2">
                        <FileSearch className="h-4 w-4" />
                        Upload / Extraire
                      </button>
                      <button type="button" onClick={loadHistory} className="btn-outline gap-2 border-white/35 text-white hover:bg-white hover:text-black">
                        <History className="h-4 w-4" />
                        Historique patient
                      </button>
                    </div>

                    {extractedText && (
                      <div className="tilt-card border border-red-500/20 bg-black/35 p-4">
                        <p className="mb-2 text-sm font-bold uppercase tracking-[0.14em] text-red-300">
                          Texte IA à valider
                        </p>
                        <textarea
                          value={extractedText}
                          onChange={(e) => setExtractedText(e.target.value)}
                          className="field-control min-h-48 resize-y"
                        />
                        <div className="mt-3 flex flex-wrap gap-3">
                          <button type="button" onClick={validatePrescription} className="btn-primary">OK, valider</button>
                          <button type="button" onClick={cancelPrescription} className="btn-outline border-white/35 text-white hover:bg-white hover:text-black">Annuler</button>
                        </div>
                      </div>
                    )}
                    {message && (
                      <p className="rounded-lg bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-200">
                        {message}
                      </p>
                    )}
                    {history.length > 0 && (
                      <div className="space-y-3">
                        <h3 className="font-display text-xl font-black">Historique patient</h3>
                        {history.map((item) => (
                          <article key={item.id} className="tilt-card border border-white/12 bg-black/35 p-4">
                            <p className="font-display font-black">{item.title}</p>
                            <p className="mt-2 whitespace-pre-line text-sm leading-7 text-white/70">{item.text}</p>
                            <p className="mt-3 text-xs font-bold text-red-300">
                              {item.doctor} - {item.validated_at}
                            </p>
                          </article>
                        ))}
                      </div>
                    )}
                  </div>
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

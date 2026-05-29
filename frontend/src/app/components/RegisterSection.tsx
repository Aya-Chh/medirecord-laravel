'use client';

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import { register, saveAuthSession } from '@/lib.ts/api';

export default function RegisterSection() {
  const router = useRouter();
  const [userType, setUserType] = useState('patient');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setSuccess(false);

    if (!name || !email || !password || !confirmPassword) {
      setError('Veuillez remplir tous les champs');
      setLoading(false);
      return;
    }

    if (password !== confirmPassword) {
      setError('Les mots de passe ne correspondent pas');
      setLoading(false);
      return;
    }

    if (password.length < 8) {
      setError('Le mot de passe doit contenir au moins 8 caracteres');
      setLoading(false);
      return;
    }

    try {
      const response = await register({
        name,
        email,
        password,
        password_confirmation: confirmPassword,
        role: userType,
      });
      saveAuthSession(response);
      setSuccess(true);
      setName('');
      setEmail('');
      setPassword('');
      setConfirmPassword('');
      router.push('/dashboard');
    } catch (err) {
      setError(err instanceof Error ? err.message : "Echec de l'inscription. Veuillez reessayer.");
    } finally {
      setLoading(false);
    }
  };

  const userTypeLabels: Record<string, string> = {
    patient: 'Patient',
    client: 'Medecin/Professionnel',
    hopital: 'Hopital/Clinique',
  };

  const userTypeDescriptions: Record<string, string> = {
    patient: 'Pour gerer votre dossier medical personnel',
    client: 'Pour suivre vos patients et gerer votre pratique',
    hopital: 'Pour gerer votre etablissement et vos equipes medicales',
  };

  return (
    <section id="inscription" className="relative overflow-hidden">
      <div className="absolute inset-0 z-0">
        <div
          className="absolute inset-0"
          style={{
            background:
              'linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.8) 100%)',
          }}
        />
      </div>

      <div className="relative z-10 max-w-[800px] mx-auto px-6 py-20 text-center">
        <h2 className="font-display font-black text-white mb-6">Creer votre dossier MediRecord</h2>
        <p className="mb-10 max-w-lg mx-auto" style={{ color: 'rgba(255,255,255,0.65)' }}>
          Choisissez votre profil pour commencer
        </p>

        {error && (
          <div
            className="mb-4 p-3 rounded-lg text-center"
            style={{
              background: 'rgba(232,25,44,0.1)',
              border: '1px solid rgba(232,25,44,0.3)',
              color: '#E8192C',
            }}
          >
            {error}
          </div>
        )}

        {success && (
          <div
            className="mb-4 p-3 rounded-lg text-center"
            style={{
              background: 'rgba(34,197,94,0.1)',
              border: '1px solid rgba(34,197,94,0.3)',
              color: '#22C55E',
            }}
          >
            Inscription reussie ! Redirection vers votre tableau de bord...
          </div>
        )}

        <div className="flex flex-wrap justify-center gap-4 mb-8">
          {['patient', 'client', 'hopital'].map((type) => (
            <label
              key={type}
              onClick={() => setUserType(type)}
              className={`relative cursor-pointer flex flex-col items-center p-6 rounded-2xl border-2 transition-all duration-300 ${
                userType === type
                  ? 'border-E8192C bg-E8192C/10'
                  : 'border-white/20 hover:border-white/40'
              }`}
              style={{ minWidth: '200px' }}
            >
              <div
                className="w-12 h-12 mb-4 flex items-center justify-center rounded-full"
                style={{
                  background: userType === type ? '#E8192C' : 'rgba(255,255,255,0.1)',
                }}
              />
              <h3 className="mb-2 font-semibold text-white">{userTypeLabels[type]}</h3>
              <p className="text-sm text-white/70">{userTypeDescriptions[type]}</p>
            </label>
          ))}
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label htmlFor="name" className="mb-2 block text-white/80">
              Nom complet
            </label>
            <input
              id="name"
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              required
              className="w-full rounded-full py-4 px-6 text-white placeholder:text-white/40 text-sm focus:outline-none transition-all duration-300"
              style={{
                background: 'rgba(255,255,255,0.1)',
                border: loading
                  ? '1.5px solid rgba(232,25,44,0.5)'
                  : '1.5px solid rgba(255,255,255,0.2)',
                backdropFilter: 'blur(10px)',
              }}
              disabled={loading}
              placeholder="Votre nom"
            />
          </div>

          <div>
            <label htmlFor="email" className="mb-2 block text-white/80">
              Email
            </label>
            <input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              className="w-full rounded-full py-4 px-6 text-white placeholder:text-white/40 text-sm focus:outline-none transition-all duration-300"
              style={{
                background: 'rgba(255,255,255,0.1)',
                border: loading
                  ? '1.5px solid rgba(232,25,44,0.5)'
                  : '1.5px solid rgba(255,255,255,0.2)',
                backdropFilter: 'blur(10px)',
              }}
              disabled={loading}
              placeholder="votre@email.ma"
            />
          </div>

          <div>
            <label htmlFor="password" className="mb-2 block text-white/80">
              Mot de passe
            </label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              minLength={8}
              className="w-full rounded-full py-4 px-6 text-white placeholder:text-white/40 text-sm focus:outline-none transition-all duration-300"
              style={{
                background: 'rgba(255,255,255,0.1)',
                border: loading
                  ? '1.5px solid rgba(232,25,44,0.5)'
                  : '1.5px solid rgba(255,255,255,0.2)',
                backdropFilter: 'blur(10px)',
              }}
              disabled={loading}
              placeholder="Mot de passe"
            />
          </div>

          <div>
            <label htmlFor="confirmPassword" className="mb-2 block text-white/80">
              Confirmez le mot de passe
            </label>
            <input
              id="confirmPassword"
              type="password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              required
              minLength={8}
              className="w-full rounded-full py-4 px-6 text-white placeholder:text-white/40 text-sm focus:outline-none transition-all duration-300"
              style={{
                background: 'rgba(255,255,255,0.1)',
                border: loading
                  ? '1.5px solid rgba(232,25,44,0.5)'
                  : '1.5px solid rgba(255,255,255,0.2)',
                backdropFilter: 'blur(10px)',
              }}
              disabled={loading}
              placeholder="Confirmez le mot de passe"
            />
          </div>

          <button type="submit" className="w-full btn-primary" disabled={loading}>
            {loading
              ? 'Creation du compte en cours...'
              : `S'inscrire en tant que ${userTypeLabels[userType]}`}
          </button>
        </form>

        <p className="mt-6 text-xs text-white/50">
          Deja inscrit ?{' '}
          <a href="#connexion" className="text-white underline">
            Connectez-vous ici
          </a>
        </p>
      </div>
    </section>
  );
}

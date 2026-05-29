'use client';

import React, { useState } from 'react';
import { useRouter } from 'next/navigation';
import { login, saveAuthSession } from '@/lib.ts/api';

export default function LoginSection() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setSuccess(false);

    if (!email || !password) {
      setError('Veuillez remplir tous les champs');
      setLoading(false);
      return;
    }

    try {
      const response = await login(email, password);
      saveAuthSession(response);
      setSuccess(true);
      setEmail('');
      setPassword('');
      router.push('/dashboard');
    } catch (err) {
      setError(
        err instanceof Error ? err.message : 'Echec de la connexion. Verifiez vos identifiants.'
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <section id="connexion" className="relative overflow-hidden">
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
        <h2 className="font-display font-black text-white mb-6">Connexion</h2>
        <p className="mb-10 max-w-lg mx-auto" style={{ color: 'rgba(255,255,255,0.65)' }}>
          Accedez a votre dossier medical securise.
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
            Connexion reussie ! Redirection vers votre tableau de bord...
          </div>
        )}

        <form onSubmit={handleSubmit} className="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="votre@email.ma"
            required
            className="flex-1 rounded-full py-4 px-6 text-white placeholder:text-white/40 text-sm focus:outline-none transition-all duration-300"
            style={{
              background: 'rgba(255,255,255,0.1)',
              border: loading
                ? '1.5px solid rgba(232,25,44,0.5)'
                : '1.5px solid rgba(255,255,255,0.2)',
              backdropFilter: 'blur(10px)',
            }}
            disabled={loading}
          />
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="Mot de passe"
            required
            className="flex-1 rounded-full py-4 px-6 text-white placeholder:text-white/40 text-sm focus:outline-none transition-all duration-300"
            style={{
              background: 'rgba(255,255,255,0.1)',
              border: loading
                ? '1.5px solid rgba(232,25,44,0.5)'
                : '1.5px solid rgba(255,255,255,0.2)',
              backdropFilter: 'blur(10px)',
            }}
            disabled={loading}
          />
          <button type="submit" className="btn-primary shrink-0" disabled={loading}>
            {loading ? 'Connexion en cours...' : 'Se connecter'}
          </button>
        </form>
      </div>
    </section>
  );
}

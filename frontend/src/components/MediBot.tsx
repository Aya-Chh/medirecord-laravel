'use client';

import React, { useState } from 'react';
import { mediBotChat } from '@/lib.ts/api';

type Message = {
  role: 'bot' | 'user';
  text: string;
};

const faq = [
  'Comment un patient consulte ses traitements ?',
  'Comment un médecin ajoute une ordonnance ?',
  "Comment fonctionne l'historique patient ?",
  'Mes données sont-elles confidentielles ?',
];

type MediBotProps = {
  defaultOpen?: boolean;
};

export default function MediBot({ defaultOpen = true }: MediBotProps) {
  const [open, setOpen] = useState(defaultOpen);
  const [input, setInput] = useState('');
  const [loading, setLoading] = useState(false);
  const [messages, setMessages] = useState<Message[]>([
    {
      role: 'bot',
      text: 'Bonjour, je suis MediBot. Bienvenue sur MediRecord. Je peux vous aider à comprendre les fonctionnalités du site.',
    },
  ]);

  const ask = async (question: string) => {
    if (!question.trim()) return;

    setMessages((current) => [...current, { role: 'user', text: question }]);
    setInput('');
    setLoading(true);

    try {
      const response = await mediBotChat(question);
      setMessages((current) => [
        ...current,
        {
          role: 'bot',
          text: `${response.data.answer}\n\n${response.data.disclaimer}`,
        },
      ]);
    } catch (err) {
      setMessages((current) => [
        ...current,
        {
          role: 'bot',
          text: err instanceof Error ? err.message : 'MediBot est momentanément indisponible.',
        },
      ]);
    } finally {
      setLoading(false);
    }
  };

  if (!open) {
    return (
      <button
        type="button"
        onClick={() => setOpen(true)}
        className="fixed bottom-5 right-5 z-50 rounded-full px-5 py-3 text-white font-semibold shadow-xl"
        style={{ background: '#E8192C' }}
      >
        MediBot
      </button>
    );
  }

  return (
    <aside className="fixed bottom-5 right-5 z-50 w-[min(92vw,380px)] rounded-2xl bg-white shadow-2xl border border-black/10 overflow-hidden">
      <div className="flex items-center justify-between px-5 py-4 bg-[#1D1D1F] text-white">
        <div>
          <p className="font-bold">MediBot</p>
          <p className="text-xs text-white/60">Assistant MediRecord</p>
        </div>
        <button type="button" onClick={() => setOpen(false)} className="text-white/70">
          Fermer
        </button>
      </div>

      <div className="max-h-[330px] overflow-y-auto p-4 space-y-3 bg-[#F5F5F7]">
        {messages.map((message, index) => (
          <div
            key={`${message.role}-${index}`}
            className={`rounded-2xl px-4 py-3 text-sm whitespace-pre-line ${
              message.role === 'bot'
                ? 'bg-white text-[#1D1D1F]'
                : 'bg-[#E8192C] text-white ml-8'
            }`}
          >
            {message.text}
          </div>
        ))}
      </div>

      <div className="p-4 space-y-3">
        <div className="flex flex-wrap gap-2">
          {faq.map((question) => (
            <button
              key={question}
              type="button"
              onClick={() => ask(question)}
              className="rounded-full bg-[#F5F5F7] px-3 py-2 text-xs text-[#1D1D1F]"
            >
              {question}
            </button>
          ))}
        </div>

        <form
          onSubmit={(event) => {
            event.preventDefault();
            ask(input);
          }}
          className="flex gap-2"
        >
          <input
            value={input}
            onChange={(event) => setInput(event.target.value)}
            placeholder="Posez votre question..."
            className="min-w-0 flex-1 rounded-full border border-black/10 px-4 py-2 text-sm outline-none"
          />
          <button
            type="submit"
            disabled={loading}
            className="rounded-full px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
            style={{ background: '#E8192C' }}
          >
            {loading ? '...' : 'OK'}
          </button>
        </form>
      </div>
    </aside>
  );
}

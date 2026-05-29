const API_BASE_URL = (process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000').replace(
  /\/$/,
  ''
);

type ApiOptions = RequestInit & {
  token?: string | null;
};

export type AuthResponse = {
  data?: {
    user?: {
      id: number;
      name: string;
      email: string;
      phone?: string | null;
    };
    token?: string;
    token_type?: string;
  };
  message?: string;
};

export class ApiError extends Error {
  status: number;
  details: unknown;

  constructor(message: string, status: number, details?: unknown) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.details = details;
  }
}

async function request<T>(path: string, options: ApiOptions = {}): Promise<T> {
  const headers = new Headers(options.headers);
  headers.set('Accept', 'application/json');

  if (!(options.body instanceof FormData)) {
    headers.set('Content-Type', 'application/json');
  }

  if (options.token) {
    headers.set('Authorization', `Bearer ${options.token}`);
  }

  const response = await fetch(`${API_BASE_URL}/api${path}`, {
    ...options,
    headers,
  });

  const payload = await response.json().catch(() => null);

  if (!response.ok) {
    const message =
      Object.values(payload?.errors || {})?.flat()?.[0] ||
      payload?.message ||
      'Une erreur est survenue avec le serveur.';

    throw new ApiError(String(message), response.status, payload);
  }

  return payload as T;
}

export function login(email: string, password: string) {
  return request<AuthResponse>('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  });
}

export function register(data: {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  role?: string;
}) {
  return request<AuthResponse>('/auth/register', {
    method: 'POST',
    body: JSON.stringify(data),
  });
}

export function saveAuthSession(auth: AuthResponse) {
  if (typeof window === 'undefined') {
    return;
  }

  const token = auth.data?.token;

  if (token) {
    window.localStorage.setItem('medirecord_token', token);
  }

  if (auth.data?.user) {
    window.localStorage.setItem('medirecord_user', JSON.stringify(auth.data.user));
  }
}

export type MediPatient = {
  id: number;
  email: string;
  cin: string;
  masked_cin: string;
  birth_date: string;
  name?: string | null;
};

export type MediDoctor = {
  id: number;
  email: string;
  first_name: string;
  last_name: string;
  name: string;
  profession: string;
  specialty: string;
  sector: string;
};

export type MediPrescription = {
  id: number;
  title: string;
  text: string;
  doctor?: string;
  specialty?: string;
  validated_at?: string;
};

export function mediPatientRegister(data: {
  email: string;
  cin: string;
  birth_date: string;
  name?: string;
}) {
  return request<{ message: string; data: { patient: MediPatient } }>('/medi/patients/register', {
    method: 'POST',
    body: JSON.stringify(data),
  });
}

export function mediPatientLogin(data: { cin: string; birth_date: string }) {
  return request<{ data: { patient: MediPatient; token: string } }>('/medi/patients/login', {
    method: 'POST',
    body: JSON.stringify(data),
  });
}

export function mediPatientDashboard(patientId: number) {
  return request<{
    data: {
      patient: MediPatient;
      doctor: { name: string; specialty: string; sector: string } | null;
      prescriptions: MediPrescription[];
    };
  }>(`/medi/patients/dashboard?patient_id=${patientId}`);
}

export function mediDoctorRegister(data: {
  email: string;
  first_name: string;
  last_name: string;
  profession: string;
  specialty: string;
  sector: string;
  professional_code: string;
}) {
  return request<{ message: string; data: { doctor: MediDoctor; daily_code: string } }>(
    '/medi/doctors/register',
    {
      method: 'POST',
      body: JSON.stringify(data),
    }
  );
}

export function mediDoctorLogin(code: string) {
  return request<{ data: { doctor: MediDoctor } }>('/medi/doctors/login', {
    method: 'POST',
    body: JSON.stringify({ code }),
  });
}

export function mediDoctorActivateCode(data: { email_code: string; new_code: string }) {
  return request<{ message: string; data: { doctor: MediDoctor } }>('/medi/doctors/activate-code', {
    method: 'POST',
    body: JSON.stringify(data),
  });
}

export function mediFindPatient(data: { doctor_id: number; cin: string; birth_date: string }) {
  return request<{ data: { patient: MediPatient } }>('/medi/doctors/find-patient', {
    method: 'POST',
    body: JSON.stringify(data),
  });
}

export function mediExtractPrescription(data: {
  doctor_id: number;
  patient_id: number;
  typed_text?: string;
  file?: File | null;
}) {
  const form = new FormData();
  form.set('doctor_id', String(data.doctor_id));
  form.set('patient_id', String(data.patient_id));
  if (data.typed_text) form.set('typed_text', data.typed_text);
  if (data.file) form.set('prescription_file', data.file);

  return request<{ data: { extracted_text: string; source_file_name?: string | null } }>(
    '/medi/doctors/extract-prescription',
    {
      method: 'POST',
      body: form,
    }
  );
}

export function mediStorePrescription(data: {
  doctor_id: number;
  patient_id: number;
  title?: string;
  raw_text?: string;
  ai_text: string;
  source_file_name?: string | null;
  status: 'validated' | 'cancelled';
}) {
  return request<{ message: string; data?: { prescription: MediPrescription } }>(
    '/medi/doctors/prescriptions',
    {
      method: 'POST',
      body: JSON.stringify(data),
    }
  );
}

export function mediPatientHistory(doctorId: number, patientId: number) {
  return request<{ data: { prescriptions: MediPrescription[] } }>(
    `/medi/doctors/patient-history?doctor_id=${doctorId}&patient_id=${patientId}`
  );
}

export function mediBotChat(message: string) {
  return request<{ data: { answer: string; disclaimer: string } }>('/medi/medibot/chat', {
    method: 'POST',
    body: JSON.stringify({ message }),
  });
}

export async function sendEmailJs(templateId: string, params: Record<string, string>) {
  const serviceId = process.env.NEXT_PUBLIC_EMAILJS_SERVICE_ID;
  const publicKey = process.env.NEXT_PUBLIC_EMAILJS_PUBLIC_KEY;

  if (!serviceId || !templateId || !publicKey) {
    return { skipped: true };
  }

  const response = await fetch('https://api.emailjs.com/api/v1.0/email/send', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      service_id: serviceId,
      template_id: templateId,
      user_id: publicKey,
      template_params: params,
    }),
  });

  if (!response.ok) {
    throw new Error("L'envoi EmailJS a échoué. Vérifiez vos variables EmailJS.");
  }

  return { skipped: false };
}

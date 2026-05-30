Description
MediRecord est une application web médicale permettant aux patients et aux médecins
de gérer les ordonnances et les traitements de manière numérique et sécurisée.
EspaceFonctionnalités principales👤 PatientInscription, connexion par CIN, consultation des ordonnances🩺 MédecinInscription pro, code activation, ajout et validation d'ordonnances🤖 MediBotAssistant conversationnel intégré

🗂️ Structure du projet
medirecord/
│
├── backend/                          # API REST — Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── PatientController.php
│   │   │   │   ├── DoctorController.php
│   │   │   │   └── PrescriptionController.php
│   │   │   └── Middleware/
│   │   └── Models/
│   │       ├── MediPatient.php
│   │       ├── MediDoctor.php
│   │       ├── MediPrescription.php
│   │       └── MediAccessLog.php
│   ├── database/
│   │   └── migrations/
│   ├── routes/
│   │   └── api.php
│   ├── .env.example
│   └── composer.json
│
├── frontend/                         # Application — Next.js / React / TypeScript
│   ├── app/
│   │   ├── patient/
│   │   │   ├── login/
│   │   │   ├── register/
│   │   │   └── dashboard/
│   │   ├── doctor/
│   │   │   ├── login/
│   │   │   ├── register/
│   │   │   ├── activate/
│   │   │   └── dashboard/
│   │   └── page.tsx                  # Page d'accueil
│   ├── components/
│   │   ├── MediBot.tsx
│   │   ├── PrescriptionCard.tsx
│   │   └── Navbar.tsx
│   ├── lib/
│   │   └── api.ts                    # Appels API centralisés
│   ├── .env.local.example
│   └── package.json
│
└── README.md

⚙️ Prérequis
Avant de commencer, assure-toi d'avoir installé :

PHP >= 8.1
Composer
Node.js >= 18
npm ou yarn
Git


🚀 Installation
1. Cloner le dépôt
bashgit clone https://github.com/ton-username/medirecord.git
cd medirecord

2. Installation du Backend (Laravel)
bash# Aller dans le dossier backend
cd backend

# Installer les dépendances PHP
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Créer la base de données SQLite
touch database/database.sqlite

# Lancer les migrations
php artisan migrate

# (Optionnel) Insérer des données de test
php artisan db:seed

# Démarrer le serveur Laravel
php artisan serve
# → API disponible sur http://localhost:8000

3. Installation du Frontend (Next.js)
bash# Aller dans le dossier frontend (depuis la racine)
cd ../frontend

# Installer les dépendances Node
npm install

# Copier le fichier d'environnement
cp .env.local.example .env.local

# Démarrer le serveur Next.js
npm run dev
# → Application disponible sur http://localhost:3000

🔧 Configuration
Backend — .env
envAPP_NAME=MediRecord
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/chemin/absolu/vers/database/database.sqlite

# CORS — autoriser le frontend
SANCTUM_STATEFUL_DOMAINS=localhost:3000
Frontend — .env.local
env# URL de l'API Laravel
NEXT_PUBLIC_API_URL=http://localhost:8000/api

# EmailJS — clés de configuration
NEXT_PUBLIC_EMAILJS_SERVICE_ID=your_service_id
NEXT_PUBLIC_EMAILJS_TEMPLATE_PATIENT=your_template_patient
NEXT_PUBLIC_EMAILJS_TEMPLATE_DOCTOR=your_template_doctor
NEXT_PUBLIC_EMAILJS_PUBLIC_KEY=your_public_key

🗄️ Base de données
MediRecord utilise SQLite avec 4 tables principales :
TableDescriptionmedi_patientsComptes et authentification des patientsmedi_doctorsComptes et codes sécurisés des médecinsmedi_prescriptionsOrdonnances (pending / validated / cancelled)medi_access_logsJournal des actions (IP + horodatage)
bash# Voir toutes les tables
php artisan migrate:status

# Réinitialiser la base de données
php artisan migrate:fresh

# Réinitialiser + données de test
php artisan migrate:fresh --seed

📡 Routes API principales
Espace Patient
MéthodeRouteDescriptionPOST/api/patient/registerInscription patientPOST/api/patient/loginConnexion (CIN + date naissance)GET/api/patient/dashboardTableau de bordGET/api/patient/prescriptionsListe des ordonnances validées
Espace Médecin
MéthodeRouteDescriptionPOST/api/doctor/registerInscription médecinPOST/api/doctor/activateActivation par code emailPOST/api/doctor/set-login-codeCréation code personnelPOST/api/doctor/loginConnexion (code personnel)POST/api/doctor/search-patientRecherche patientPOST/api/doctor/prescriptionsAjouter une ordonnancePATCH/api/prescriptions/{id}/validateValider une ordonnancePATCH/api/prescriptions/{id}/cancelAnnuler une ordonnance

🔐 Sécurité
MesureDétailHachage des codesbcrypt via Laravel — jamais en clairCode personnelMinimum 7 caractères + 1 caractère spécial obligatoireAnti brute-forceMiddleware throttle Laravel (limite tentatives par IP)JournalisationTable medi_access_logs — toutes les actions tracéesValidation médicaleOrdonnance enregistrée uniquement après validation explicite

📜 Historique des commits
bash# Voir l'historique dans le terminal
git log --oneline

# Exemple de sortie :
# a1b2c3d  style: amélioration UI et navigation
# d4e5f6g  fix: correction connexion patient
# h7i8j9k  feat: ajout MediBot
# l0m1n2o  feat: système validation/annulation ordonnances
# p3q4r5s  feat: upload ordonnance PDF/image
# t6u7v8w  feat: intégration EmailJS
# x9y0z1a  feat: espace médecin complet
# b2c3d4e  feat: espace patient complet
# f5g6h7i  feat: migrations SQLite (4 tables)
# j8k9l0m  feat: initialisation Laravel + Next.js

🧪 Tests
bash# Tests backend Laravel
cd backend
php artisan test

# Tests frontend Next.js
cd frontend
npm run lint
npm run build

📦 Déploiement
bash# Build production frontend
cd frontend
npm run build
npm start

# Backend Laravel en production
cd backend
php artisan config:cache
php artisan route:cache
php artisan optimize

🤖 MediBot
MediBot est un assistant conversationnel intégré à l'interface.
Il répond aux questions fréquentes sur l'utilisation de l'application.

⚠️ MediBot ne remplace pas un professionnel de santé.
Il est uniquement destiné à guider l'utilisateur dans l'application.


👩‍💻 Auteure
Aya Chraibi
Licence en Informatique — Développement Web
Faculté des Sciences et Techniques — Marrakech
Université Cadi Ayyad
Année universitaire : 2025 / 2026

👩‍🏫 Encadrante
Mme. Sara Qacimi
Faculté des Sciences et Techniques — Marrakech

📄 Licence
Ce projet est réalisé dans le cadre d'un projet universitaire.
© 2026 Aya Chraibi — Tous droits réservés.

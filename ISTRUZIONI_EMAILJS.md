# ⚙️ Configurazione EmailJS - Pagina Candidati

## Cosa è stato fatto
La pagina **candidati.html** è stata creata con un form completo che invia i dati a **info@lanternacorporation.com** usando **EmailJS**, un servizio gratuito che permette di inviare email direttamente da HTML/JavaScript senza necessità di backend server.

---

## 📋 Passi di Setup (IMPORTANTE!)

### 1. **Registrati su EmailJS**
- Vai a: https://www.emailjs.com/
- Clicca su **"Sign Up"** (è gratuito!)
- Crea un account con email valida

### 2. **Crea un Service (Email Provider)**
- Nel dashboard di EmailJS, vai a **"Email Services"**
- Clicca **"Add Service"**
- Seleziona **"Gmail"** (oppure il provider che usi)
- Segui le istruzioni per collegare Gmail
- **Copia il Service ID** (es: `service_abc123xyz`) - **Lo userai dopo**

### 3. **Crea un Email Template**
- Nel dashboard, vai a **"Email Templates"**
- Clicca **"Create New Template"**
- Configura come segue:

**Template Name:** `candidati_form` (o come preferisci)

**Template Content (Body):**
```
Nuova candidatura da: {{nome}} {{cognome}}

📧 Email: {{email}}
📱 Telefono: {{telefono}}
📍 Città: {{citta}}
🎂 Data di nascita: {{nascita}}
💼 Esperienza: {{esperienza}}

Messaggio:
{{messaggio}}

---
Questa candidatura è stata inviata dal form del sito Lanterna Corporation
```

**To Email:** `{{recipient_email}}` (lascia così!)

- Clicca **"Save"**
- **Copia il Template ID** (es: `template_abc123xyz`) - **Lo userai dopo**

### 4. **Aggiorna il file candidati.html**
Apri **candidati.html** e trova questa riga (circa alla riga 228):

```javascript
emailjs.init('SERVICE_ID_PLACEHOLDER'); // Sostituisci con il tuo Service ID
```

**Sostituisci** `SERVICE_ID_PLACEHOLDER` con il tuo **Service ID** ricavato dal passo 2.

Poi trova questa riga (circa alla riga 248):

```javascript
emailjs.send('SERVICE_ID', 'TEMPLATE_ID', formData)
```

**Sostituisci:**
- `'SERVICE_ID'` → il tuo **Service ID** (stesso del passo precedente)
- `'TEMPLATE_ID'` → il tuo **Template ID** dal passo 3

**Esempio finale:**
```javascript
emailjs.init('service_abc123xyz');
// ...
emailjs.send('service_abc123xyz', 'template_abc123xyz', formData)
```

---

## ✅ Verifica che Funziona

### In locale (durante sviluppo):
- Il form è visibile e funzionale
- EmailJS non caricherà da file:// (normale, è una limitazione di sicurezza del browser)
- Non vedrai messaggi di errore importanti finché non pubblichi online

### Quando pubblichi online:
1. Carica i file su un hosting (es: Netlify, Vercel, GitHub Pages)
2. Apri il sito live
3. Riempi il form di test
4. Clicca **"Invia candidatura"**
5. Dovresti ricevere l'email a `info@lanternacorporation.com` ✅

---

## 🔒 Sicurezza & Best Practices

- **Service ID e Template ID** possono stare pubblici nel codice (sono per il frontend)
- **Non** condividere credenziali Gmail nel codice
- EmailJS gestisce automaticamente l'autenticazione tramite il Service collegato

---

## 🐛 Troubleshooting

**Problema:** "Service ID is not provided"
- ✅ Soluzione: Controlla che hai aggiornato `SERVICE_ID_PLACEHOLDER` nel codice

**Problema:** "Template not found"
- ✅ Soluzione: Verifica che il Template ID nel codice è esatto

**Problema:** "Failed to load resource" (errore 404)
- ✅ Soluzione: Normale in locale, scompare quando pubblichi online

**Problema:** Non ricevo l'email
- ✅ Controlla lo spam
- ✅ Verifica che il Service di Gmail sia collegato correttamente
- ✅ Apri la console del browser (F12 → Console) e guarda gli errori

---

## 📞 Supporto

- **EmailJS Docs:** https://www.emailjs.com/docs/
- **Dashboard EmailJS:** https://dashboard.emailjs.com/

---

**Fatto!** Una volta configurato, il form della pagina candidati invierà automaticamente le candidature a info@lanternacorporation.com ✨

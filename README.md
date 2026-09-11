# Lanterna Corporation — Sito web 🚀

> *"Dove le persone fanno la differenza, ogni giorno. ✨"*

Sito vetrina e pagina di recruiting per **Lanterna Corporation**, società di direct marketing
con sede a Milano che si occupa di fundraising e raccolta fondi per organizzazioni no profit.

All'inizio era solo una cosa mia: nei ritagli di tempo mi sono messa a rifare il sito con i
colori aziendali, tanto per far vedere l'idea in giro. L'ho mostrata, è piaciuta, e a un certo
punto la direzione ha detto che lo voleva davvero online. Da lì il mockup è diventato il sito
vero: form di candidatura che funziona e un backend che riceve i CV.

Design e sviluppo sono miei (Anna Maria Basangeac). Logo e marchi sono di Lanterna Corporation.

---

## Cosa contiene

- **Landing page single-page** (`index.html`) con sezioni Home, About, Cosa offriamo, Team,
  Gallery e Contatti, navigazione con scroll fluido e ancore.
- **Pagina candidature** (`candidati.html`) con form completo: dati anagrafici, lettera di
  presentazione, upload del CV e consenso al contatto e al trattamento dati.
- **Cookie Policy** (`cookie.html`) e **Privacy Policy** (`privacy-policy.html`), con banner
  di consenso cookie richiamabile da entrambe le pagine principali del sito.
- **Backend PHP** (`send-email.php`) che riceve la candidatura, la valida lato server e la
  inoltra via email con il CV in allegato.

## Funzionalità principali

- Layout **completamente responsive**, pensato mobile-first, con menu hamburger animato.
- **Gallery a carosello infinito** (marquee CSS) con le foto del team.
- **Pulsante "torna su"** con animazione custom in SVG: una tazzina di caffè che si svuota
  mentre scendi nella pagina e si riempie mentre risali.
- **Banner cookie** a comparsa (con leggero ritardo), che ricorda la scelta dell'utente in
  `localStorage` e rimanda alla Cookie Policy per i dettagli.
- **Form di candidatura asincrono**: invio via `fetch` senza ricaricare la pagina, con stati
  di caricamento, successo ed errore, checkbox di consenso al contatto e di accettazione
  della privacy policy.
- **Validazione server-side**: campi obbligatori, formato email, protezione da *header
  injection*, controllo di estensione (`pdf`, `doc`, `docx`) e dimensione (max 8 MB) del CV.
- Invio email in `multipart/mixed` con allegato in base64 e mittente configurato per non
  finire dritto nello spam (vedi sotto).

## Stack

| Ambito      | Tecnologie                                                        |
|-------------|-----------------------------------------------------------------|
| Markup      | HTML5 semantico, attributi ARIA di base                          |
| Stile       | CSS3 scritto a mano (~2.500 righe), Flexbox e Grid, nessun framework |
| Interazioni | JavaScript vanilla (no dipendenze)                               |
| Backend     | PHP 8, funzione `mail()`                                         |
| Font        | Plus Jakarta Sans (Google Fonts)                                 |
| Hosting     | Aruba Hosting Basic Linux, deploy via SFTP                       |

## Struttura del progetto

```
.
├── index.html          # Landing page
├── candidati.html      # Form di candidatura
├── cookie.html          # Cookie Policy
├── privacy-policy.html  # Privacy Policy
├── send-email.php      # Endpoint per la ricezione delle candidature
├── style.css           # Foglio di stile unico
├── assets/
│   ├── loghi/          # Logo aziendale e icone social
│   ├── galleria/       # Foto del team per la gallery
│   ├── team/           # Ritratto della founder
│   └── *.png           # Illustrazioni della sezione "Cosa offriamo"
├── LICENSE
└── README.md
```

## Come eseguirlo in locale

La parte statica non richiede build: basta aprire `index.html` nel browser.

Per provare anche l'invio del form serve un interprete PHP:

```bash
php -S localhost:8000
```

Poi apri <http://localhost:8000>. In locale `mail()` di solito non consegna davvero nulla:
serve comunque a verificare validazione dei campi e risposte HTTP dell'endpoint.

## Come funziona l'invio delle candidature

La mail non parte dal PC del candidato: la genera il server Aruba con `mail()`. Nel messaggio:

- **`From:`** è `info@lanternacorporation.com`, non l'indirizzo di chi ha compilato il form.
- **`Reply-To:`** è l'indirizzo del candidato, così rispondere dalla casella apre subito una
  mail verso di lui.
- **destinatario** è `info@lanternacorporation.com`, la casella aziendale che riceve tutto.

Il mittente **deve** essere una casella reale del dominio `lanternacorporation.com`. Motivo:
ogni dominio dichiara nel DNS quali server sono autorizzati a spedire a suo nome (record SPF),
e quello di Lanterna autorizza i server Aruba. Se come mittente si mettesse l'indirizzo del
visitatore (`@gmail.com`, `@libero.it`…), Aruba starebbe spedendo "a nome" di domini che non
la autorizzano: il controllo SPF fallisce e la mail viene rifiutata o marcata come spam. Con
un mittente del dominio, spedito da Aruba, il controllo passa e la mail arriva in posta in
arrivo. Il quinto parametro di `mail()` (`-f`) imposta lo stesso mittente a livello di
*envelope*, che è ciò che l'SPF verifica davvero.

## Deploy

I file si caricano via SFTP sull'hosting condiviso Aruba. La configurazione SFTP locale
(`.vscode/sftp.json`) è esclusa dal versioning tramite `.gitignore`.

## Licenza

Codice rilasciato sotto licenza [MIT](LICENSE). Logo, marchi, testi e fotografie sono di
proprietà di Lanterna Corporation e non rientrano nella licenza.

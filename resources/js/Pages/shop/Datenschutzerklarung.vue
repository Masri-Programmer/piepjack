<template>
  <div class="max-w-4xl mx-auto px-6 py-12 tracking-normal leading-relaxed">
    <h1
      class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-center mb-10"
    >
      Datenschutzerklärung
    </h1>
    <p class="text-lg font-semibold">Stand: {{ LAST_UPDATED }}</p>
    <p>
      Wir von {{ COMPANY_NAME }} nehmen den Schutz Ihrer persönlichen Daten sehr
      ernst. Diese Datenschutzerklärung informiert Sie über die Erhebung,
      Verarbeitung und den Schutz Ihrer personenbezogenen Daten.
    </p>

    <h2 class="text-xl font-semibold mt-6">1. Verantwortliche Stelle</h2>
    <p>{{ COMPANY_NAME }}, {{ COMPANY_EMAIL }}, {{ COMPANY_PHONE }}</p>

    <h2 class="text-xl font-semibold mt-6">
      2. Erhebung und Verarbeitung personenbezogener Daten
    </h2>
    <ul class="list-disc list-inside space-y-2">
      <li>
        <strong>Beim Besuch unserer Website:</strong> IP-Adresse, Browsertyp,
        Betriebssystem, Zugriffszeit, besuchte Seiten.
      </li>
      <li>
        <strong>Beim Einkauf:</strong> Name, Adresse, E-Mail, Telefonnummer,
        Zahlungsdaten.
      </li>
      <li>
        <strong>Bei der Kontaktaufnahme:</strong> Name, E-Mail, Telefonnummer,
        Nachrichteninhalt.
      </li>
    </ul>

    <h2 class="text-xl font-semibold mt-6">
      3. Weitergabe von Daten an Dritte
    </h2>
    <ul class="list-disc list-inside space-y-2">
      <li>Versanddienstleister (z. B. DHL, Hermes)</li>
      <li>Zahlungsanbieter (z. B. PayPal, Kreditkartenanbieter)</li>
      <li>IT-Dienstleister für Website-Wartung</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6">4. Speicherdauer</h2>
    <p>
      Ihre Daten werden nur so lange gespeichert, wie es erforderlich oder
      gesetzlich vorgeschrieben ist.
    </p>

    <h2 class="text-xl font-semibold mt-6">5. Ihre Rechte</h2>
    <ul class="list-disc list-inside space-y-2">
      <li>Recht auf Auskunft, Berichtigung und Löschung Ihrer Daten</li>
      <li>Recht auf Datenübertragbarkeit</li>
      <li>Recht auf Widerspruch gegen die Datenverarbeitung</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6">6. Cookies und Tracking-Tools</h2>
    <p>Unsere Website verwendet Cookies zur Optimierung der Nutzererfahrung.</p>
    <ul class="list-disc list-inside space-y-2">
      <li>Notwendige Cookies für den Website-Betrieb</li>
      <li>Analyse-Cookies zur Verbesserung unserer Services</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6">7. Datensicherheit</h2>
    <p>
      Wir setzen technische und organisatorische Maßnahmen ein, um Ihre Daten zu
      schützen (z. B. SSL-Verschlüsselung).
    </p>

    <h2 class="text-xl font-semibold mt-6">
      8. Änderungen dieser Datenschutzerklärung
    </h2>
    <p>
      Diese Datenschutzerklärung kann bei rechtlichen oder technischen
      Änderungen aktualisiert werden.
    </p>

    <h2 class="text-xl font-semibold mt-6">9. Kontakt</h2>
    <p>Bei Fragen kontaktieren Sie uns unter: {{ COMPANY_EMAIL }}</p>
  </div>
</template>

<script setup>
const COMPANY_NAME = "Piepjack";
const COMPANY_EMAIL = "info@piepjack-clothing.de";
const COMPANY_PHONE = "[Telefonnummer 📱]";
const LAST_UPDATED = "01.01.2025";
</script>

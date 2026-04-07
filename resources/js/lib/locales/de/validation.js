export default {
    validation: {
        email: {
            required: "E-Mail-Adresse eingeben",
            invalid: "Gib eine gültige E-Mail-Adresse ein",
            max: "E-Mail darf {max} Zeichen nicht überschreiten",
        },
        firstName: "Gib einen Vornamen ein",
        firstNameMax: "Vorname darf {max} Zeichen nicht überschreiten",
        lastName: "Gib einen Nachnamen ein",
        lastNameMax: "Nachname darf {max} Zeichen nicht überschreiten",
        address: "Gib eine Adresse ein",
        addressMax: "Adresse darf {max} Zeichen nicht überschreiten",
        land: "Gib ein Land ein",
        zip: "Gib eine Postleitzahl ein",
        zipMax: "Postleitzahl darf {max} Zeichen nicht überschreiten",
        phone: "Format nicht verfügbar",
        rating: "Bitte geben Sie eine Bewertung ann",
        comment: "Bitte geben Sie einen Kommentar einn",
        city: "Gib eine Stadt ein",
        cityMax: "Stadt darf {max} Zeichen nicht überschreiten",
        state: "Bitte geben Sie ein Bundesland ein",
        stateMax: "Bundesland darf {max} Zeichen nicht überschreiten",
        error: "Ein Fehler aufgetreten",
    },
    messages: {
        loading: "Lade...",
        error: "Ein Fehler ist aufgetreten",
    },
    form: {
        email: "E-Mail-Adresse",
        orderNumber: "Bestellnummer (e.g GREFIYOO2Q)",
    },
    api: {
        post: {
            success: "{resource} wurde erfolgreich gespeichert",
            error: "Fehler beim Erstellen von {resource}",
        },
        delete: {
            cancel: "Löschen {resource} Storniert",
            confirm: "Sind Sie sicher, dass Sie löschen möchten {resource}",
            success: "{resource} wurde erfolgreich gelöscht",
            error: "Fehler beim Löschen von {resource}",
        },
        get: {
            success: "{resource} wurde erfolgreich abgerufen",
            error: "Fehler beim Abrufen von {resource}",
        },
        put: {
            success: "{resource} wurde erfolgreich aktualisiert",
            error: "Fehler beim Aktualisieren von {resource}",
        },
        error: "Ein Fehler ist aufgetreten",
    },
    error: {
        notFound: "404",
        message:
            "Die gesuchte Seite konnte nicht gefunden werden. Bitte nutzen Sie die Navigation oder den Button unten, um zu unserer Website zurückzukehren.",
        continueShopping: "weiter einkaufen",
        getProducts: "Fehler laden Produkte",
    },
    success: {
        order: "Bestellung",
        return: "Rückabe",
        title: "Zahlung abgeschlossen!",
        thankYou: "Vielen Dank für Ihre sichere Online-Zahlung.",
        checkEmail:
            "Sie können Ihre E-Mails überprüfen, um weitere Informationen zu Ihrer {payment} zu erhalten.",
        processingTitle: "Zahlung wird verarbeitet...",
        processingDesc:
            "Bitte warten Sie, während wir Ihre Zahlung bestätigen.",
        timeoutTitle: "Bestellung erhalten",
        timeoutDesc:
            "Wir haben Ihre Bestellung erhalten, aber die Zahlungsbestätigung dauert länger als erwartet.",
        timeoutCheckEmail: "Bitte überprüfen Sie Ihre E-Mails auf Updates.",
    },
};

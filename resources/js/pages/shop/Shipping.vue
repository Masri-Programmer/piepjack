<template>
    <div
        class="grid place-content-center justify-items-center w-full h-full px-4 sm:px-8"
    >
        <h1
            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-center mb-8"
        >
            SHIPPING
        </h1>

        <p class="md:w-3/5 text-sm leading-relaxed text-center mb-6">
            Nachdem du deine Bestellung aufgegeben hast, erhälst du eine
            Bestätigungs-E-Mail mit allen Einzelheiten zu deinem Kauf,
            einschließlich einer Tracking-Nummer. Mit dieser Nummer kannst du
            den Verlauf deiner Bestellung in Echtzeit verfolgen und weißt so
            immer, wo sich dein Paket befindet.
        </p>

        <div class="w-full overflow-x-auto grid place-content-center">
            <table
                class="w-full max-w-3xl border-collapse border border-gray shadow-md"
            >
                <thead class="bg-gray-100">
                    <tr>
                        <th
                            class="border border-gray px-4 py-2 text-center text-accent"
                        >
                            Land
                        </th>
                        <th
                            class="border border-gray px-4 py-2 text-center text-accent"
                        >
                            Lieferung
                        </th>
                        <th
                            class="border border-gray px-4 py-2 text-center text-accent"
                        >
                            Kosten
                        </th>
                        <th
                            class="border border-gray px-4 py-2 text-center text-accent"
                        >
                            Kostenlose Lieferung
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50 hover:text-accent">
                        <td class="border border-gray px-4 py-2 text-center">
                            Deutschland
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            2-4
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            €3,95
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            bei Bestellungen über €70
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 hover:text-accent">
                        <td class="border border-gray px-4 py-2 text-center">
                            Österreich
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            2-4
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            €4,95
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            bei Bestellungen über €70
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 hover:text-accent">
                        <td class="border border-gray px-4 py-2 text-center">
                            Schweiz & andere EU-Länder
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            3-5
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            €9,99
                        </td>
                        <td class="border border-gray px-4 py-2 text-center">
                            -
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h6 class="font-bold text-center mt-6">
            Nach einzelnen Drops kann sich die Lieferzeit auf 10-16 Tage
            verlängern.
        </h6>
    </div>
</template>

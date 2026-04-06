<template>
    <div
        class="max-w-5xl mx-auto px-6 py-16 sm:py-24 selection:bg-main selection:text-accent"
    >
        <header
            class="mb-16 border-b-[12px] border-border pb-10 text-center md:text-left"
        >
            <p
                class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
            >
                {{ $t("pages.shipping.logistics") }}
            </p>
            <h1
                class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
            >
                {{ $t("pages.shipping.title") }}
            </h1>
        </header>

        <div class="grid md:grid-cols-2 gap-12 mb-20">
            <div class="border-l-4 border-main pl-6">
                <p
                    class="text-lg leading-relaxed font-medium text-foreground italic"
                >
                    {{ $t("pages.shipping.tracking_info_header") }}
                </p>
            </div>
            <p
                class="text-sm leading-relaxed text-muted-foreground uppercase tracking-wide"
            >
                {{ $t("pages.shipping.tracking_info_description") }}
            </p>
        </div>

        <div class="w-full overflow-hidden border-2 border-main bg-background">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr
                            class="bg-main text-accent uppercase text-xs tracking-[0.2em]"
                        >
                            <th class="p-6 border-r border-accent/20">
                                {{ $t("pages.shipping.table.country") }}
                            </th>
                            <th
                                class="p-6 border-r border-accent/20 text-center"
                            >
                                {{ $t("pages.shipping.table.delivery") }}
                            </th>
                            <th
                                class="p-6 border-r border-accent/20 text-center"
                            >
                                {{ $t("pages.shipping.table.cost") }}
                            </th>
                            <th class="p-6 text-center">
                                {{ $t("pages.shipping.table.free_shipping") }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-border">
                        <tr class="hover:bg-muted transition-colors group">
                            <td
                                class="p-6 font-bold uppercase italic border-r-2 border-border"
                            >
                                {{ $t("pages.shipping.countries.germany") }}
                            </td>
                            <td
                                class="p-6 text-center border-r-2 border-border font-mono"
                            >
                                2-4
                            </td>
                            <td
                                class="p-6 text-center border-r-2 border-border font-bold"
                            >
                                €3,95
                            </td>
                            <td
                                class="p-6 text-center bg-accent_black/5 group-hover:bg-transparent transition-colors"
                            >
                                {{ $t("pages.shipping.over") }} €70
                            </td>
                        </tr>
                        <tr class="hover:bg-muted transition-colors group">
                            <td
                                class="p-6 font-bold uppercase italic border-r-2 border-border"
                            >
                                {{ $t("pages.shipping.countries.austria") }}
                            </td>
                            <td
                                class="p-6 text-center border-r-2 border-border font-mono"
                            >
                                2-4
                            </td>
                            <td
                                class="p-6 text-center border-r-2 border-border font-bold"
                            >
                                €4,95
                            </td>
                            <td
                                class="p-6 text-center bg-accent_black/5 group-hover:bg-transparent transition-colors"
                            >
                                {{ $t("pages.shipping.over") }} €70
                            </td>
                        </tr>
                        <tr class="hover:bg-muted transition-colors group">
                            <td
                                class="p-6 font-bold uppercase italic border-r-2 border-border"
                            >
                                {{
                                    $t("pages.shipping.countries.switzerland_eu")
                                }}
                            </td>
                            <td
                                class="p-6 text-center border-r-2 border-border font-mono"
                            >
                                3-5
                            </td>
                            <td
                                class="p-6 text-center border-r-2 border-border font-bold"
                            >
                                €9,99
                            </td>
                            <td class="p-6 text-center opacity-30">—</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 p-8 bg-accent_dark border-2 border-main text-center">
            <h6 class="font-bold uppercase tracking-widest text-sm italic">
                {{ $t("pages.shipping.warning") }}
            </h6>
        </div>
    </div>
</template>

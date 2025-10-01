import forms from "@tailwindcss/forms";
const animate = require("tailwindcss-animate");
const { setupInspiraUI } = require("@inspira-ui/plugins");

/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/js/**/*.{vue,js}"],

    theme: {
        container: {
            center: true,
            padding: {
                DEFAULT: ".5rem",
                sm: "1.5rem",
                lg: "2rem",
                xl: "2.5rem",
                "2xl": "3rem",
            },
            margin: {
                DEFAULT: ".5rem",
                sm: "1.5rem",
                lg: "2rem",
                xl: "2.5rem",
                "2xl": "3rem",
            },
        },
        extend: {
            keyframes: {
                "accordion-down": {
                    from: { height: "0" },
                    to: { height: "var(--reka-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--reka-accordion-content-height)" },
                    to: { height: "0" },
                },
                "caret-blink": {
                    "0%,70%,100%": { opacity: "1" },
                    "20%,50%": { opacity: "0" },
                },
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
                "caret-blink": "caret-blink 1.25s ease-out infinite",
                "accordion-up": "accordion-up 0.2s ease-out",
                "spin-slow": "spin 5s linear infinite",
            },
            fontFamily: {
                sans: ["AvenirNext-Bold", "sans-serif"],
            },
            colors: {
                accent: "var(--accent)",
                accent_light: "var(--accent_light)",
                accent_dark: "var(--accent_dark)",
                accent_black: "var(--accent_black)",
                main: "var(--main)",
                gray: "var(--gray)",
            },
            borderRadius: {
                xl: "calc(var(--radius) + 4px)",
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },
        },
    },

    plugins: [
        forms,
        animate,
        setupInspiraUI,
        require("@tailwindcss/forms"),
        require("@tailwindcss/aspect-ratio"),
    ],
};
